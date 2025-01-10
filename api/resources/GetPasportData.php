<?php

namespace api\resources;

use Yii;
use yii\httpclient\Client;

class GetPasportData
{
    public static function birthdatSeriaNumber($birthday , $seria , $number)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isDate($birthday)) {
            $errors[] = [_e('Tug\'ilgan sana notog\'ri yuborildi.')];
        }
        if (!isValidPassportSeries($seria)) {
            $errors[] = [_e('Pasport seriya notog\'ri yuborildi.')];
        }
        if (!isValidPassportNumber($number)) {
            $errors[] = [_e('Pasport raqam notog\'ri yuborildi.')];
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => $errors];
        }

        $birthday = date('Y-m-d' , strtotime($birthday));
        $client = new Client();
        $url = 'https://api.online-mahalla.uz/api/v1/public/tax/passport';
        $params = [
            'series' => $seria,
            'number' => $number,
            'birth_date' => $birthday,
        ];
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();
        if ($response->isOk) {
            $responseData = $response->data;
            $passport = $responseData['data']['info']['data'];
            $data = [
                'first_name' => $passport['name'],
                'last_name' => $passport['sur_name'],
                'middle_name' => $passport['patronymic_name'],
                'passport_number' => $number,
                'passport_serial' => $seria,
                'passport_pin' => (string)$passport['pinfl'],
                'passport_issued_date' => date("Y-m-d" , strtotime($passport['expiration_date'])),
                'passport_given_date' => date("Y-m-d" , strtotime($passport['given_date'])),
                'passport_given_by' => $passport['given_place'],
                'gender' => $passport['gender'],
                'address' => $passport['address'],
            ];
            $transaction->commit();
            return ['is_ok' => true , 'data' => $data];
        }

        $errors[] = ['Ma\'lumotlarni olishda xatolik yuz berdi.'];

        $transaction->rollBack();
        return ['is_ok' => false, 'errors' => $errors];
    }

    public static function pinfl($pin)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isValidPassportPin($pin)) {
            $errors[] = [_e('Pinfl notog\'ri yuborildi.')];
        }

        if (count($errors) > 0) {
            $transaction->rollBack();
            return ['is_ok' => false, 'errors' => $errors];
        }

        $url = 'https://subsidiya.idm.uz/api/applicant/get-photo';

        $data = json_encode([
            'pinfl' => (int)$pin
        ]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode('ikbol:ikbol123321')
        ]);

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        if ($response['data']) {
            $passport = $response['data'];
            $data = [
                'first_name' => $passport['nameLatin'],
                'last_name' => $passport['surnameLatin'],
                'middle_name' => $passport['patronymLatin'],
                'passport_number' => $passport['docNumber'],
                'passport_serial' => $passport['docSeria'],
                'passport_pin' => (string)$passport['pinfl'],
                'passport_issued_date' => $passport['docDateBegin'],
                'passport_given_date' => $passport['docDateEnd'],
                'passport_given_by' => $passport['docGivePlace'],
                'gender' => $passport['sex'],
            ];
            $transaction->commit();
            return ['is_ok' => true , 'data' => $data];
        }

        $errors[] = ['Ma\'lumotlarni olishda xatolik yuz berdi.'];

        $transaction->rollBack();
        return ['is_ok' => false, 'errors' => $errors];
    }

}
