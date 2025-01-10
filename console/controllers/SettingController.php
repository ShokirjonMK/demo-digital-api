<?php

namespace console\controllers;

use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubject;
use common\models\model\Group;

use common\models\model\PasswordEncrypts;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use common\models\model\SubjectVedomst;
use common\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\console\Controller;


class SettingController extends Controller
{

    public function actionStudentGender()
    {
        $students = Student::find()
            ->where(['is_deleted' => 0 , 'status' => 10])
            ->all();
        foreach ($students as $student) {
            $profile = $student->profile;
            $student->gender = $profile->gender;
            $student->update(false);
        }
    }
    public function actionEduVedomst()
    {
        $subjects = EduSemestrSubject::find()
            ->where([
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->all();
        foreach ($subjects as $subject) {
            for ($i = 1; $i <= 3; $i++) {
                $vedomst = SubjectVedomst::findOne([
                    'edu_semestr_subject_id' => $subject->id,
                    'edu_semestr_id' => $subject->edu_semestr_id,
                    'edu_plan_id' => $subject->eduSemestr->edu_plan_id,
                    'type' => $i
                ]);
                if (!$vedomst) {
                    $new = new SubjectVedomst();
                    $new->edu_semestr_subject_id = $subject->id;
                    $new->edu_semestr_id = $subject->edu_semestr_id;
                    $new->edu_plan_id = $subject->eduSemestr->edu_plan_id;
                    $new->type = $i;
                    $new->save(false);
                }
            }
        }
    }

    public function actionStdG()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $studentGroups = StudentGroup::find()
            ->where([
                'is_deleted' => 0,
                'status' => 1
            ])
            ->all();

        foreach ($studentGroups as $studentGroup) {
            $eduSemestr = EduSemestr::findOne($studentGroup->edu_semestr_id);
            $eduSemestrSubjects = $eduSemestr->eduSemestrSubjects;
            $result = self::studentSemestrSubject($studentGroup , $eduSemestrSubjects);
            if (!$result['is_ok']) {
                foreach ($result['errors'] as $err) {
                    $errors[] = $err;
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            echo "1111";
        } else {
            $transaction->rollBack();
            dd($errors);
            echo "2222 \n";
        }
    }


    public static function studentSemestrSubject($model , $eduSemestrSubjects)
    {
        $errors = [];
        foreach ($eduSemestrSubjects as $subject) {

            $isStudentSubject = StudentSemestrSubject::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'student_id' => $model->student_id,
                'is_deleted' => 0
            ]);

            $eduSemestrSubjectExamTypes = $subject->eduSemestrExamsTypes;

            if (!$isStudentSubject) {
                $studentSemestrSubject = new StudentSemestrSubject();
                $studentSemestrSubject->edu_plan_id = $model->edu_plan_id;
                $studentSemestrSubject->edu_semestr_id = $model->edu_semestr_id;
                $studentSemestrSubject->edu_semestr_subject_id = $subject->id;
                $studentSemestrSubject->student_id = $model->student_id;
                $studentSemestrSubject->student_user_id = $model->student->user_id;
                $studentSemestrSubject->faculty_id = $model->faculty_id;
                $studentSemestrSubject->direction_id = $model->direction_id;
                $studentSemestrSubject->edu_form_id = $model->edu_form_id;
                $studentSemestrSubject->edu_year_id = $model->edu_year_id;
                $studentSemestrSubject->course_id = $model->eduSemestr->course_id;
                $studentSemestrSubject->semestr_id = $model->semestr_id;
                if (!$studentSemestrSubject->validate()) {
                    $errors[] = [ 'studentSemestrSubject'];
                } else {
                    $studentSemestrSubject->save(false);
                    $studentVedomst = new StudentSemestrSubjectVedomst();
                    $studentVedomst->student_semestr_subject_id = $studentSemestrSubject->id;
                    $studentVedomst->subject_id = $subject->subject_id;
                    $studentVedomst->edu_year_id = $model->edu_year_id;
                    $studentVedomst->semestr_id = $model->semestr_id;
                    $studentVedomst->student_id = $model->student_id;
                    $studentVedomst->student_user_id = $studentSemestrSubject->student_user_id;
                    $studentVedomst->group_id = $model->group_id;
                    $studentVedomst->vedomst = 1;
                    if (!$studentVedomst->validate()) {
                        $errors[] = ['student Vedomst validate'];
                    } else {
                        $studentVedomst->save(false);
                        foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                            $studentMark = new StudentMark();
                            $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                            $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                            $studentMark->group_id = $model->group_id;
                            $studentMark->student_id = $model->student_id;
                            $studentMark->student_user_id = $studentVedomst->student_user_id;
                            $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                            $studentMark->edu_semestr_subject_id = $subject->id;
                            $studentMark->subject_id = $subject->subject_id;
                            $studentMark->edu_plan_id = $model->edu_plan_id;
                            $studentMark->edu_semestr_id = $model->edu_semestr_id;
                            $studentMark->faculty_id = $model->faculty_id;
                            $studentMark->direction_id = $model->direction_id;
                            $studentMark->semestr_id = $model->semestr_id;
                            $studentMark->course_id = $studentSemestrSubject->course_id;
                            $studentMark->vedomst = 1;
                            $studentMark->student_semestr_subject_vedomst_id = $studentVedomst->id;
                            $studentMark->save(false);
//                            if (!$studentMark->validate()) {
//                                $errors[] = ['Student Mark Validate Errors'];
//                            }
                        }
                    }
                }

            } else {

                $queryVedomst = StudentSemestrSubjectVedomst::findOne([
                    'subject_id' => $subject->subject_id,
                    'student_semestr_subject_id' => $isStudentSubject->id,
                    'is_deleted' => 0,
                    'vedomst' => 1,
                ]);

                $studentGroup = StudentGroup::findOne([
                    'edu_semestr_id' => $isStudentSubject->edu_semestr_id,
                    'student_id' => $isStudentSubject->student_id,
                    'status' => 1,
                    'is_deleted' => 0
                ]);

                if ($queryVedomst) {

                    if ($studentGroup) {
                        $queryVedomst->group_id = $studentGroup->group_id;
                        $queryVedomst->save(false);
                    }

                    foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                        $qStudentMark = StudentMark::findOne([
                            'student_semestr_subject_vedomst_id' => $queryVedomst->id,
                            'edu_semestr_exams_type_id' => $eduSemestrSubjectExamType->id,
                            'is_deleted' => 0,
                            'vedomst' => 1
                        ]);
                        if (!$qStudentMark) {
                            $studentMark = new StudentMark();
                            $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                            $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                            $studentMark->group_id = $model->group_id;
                            $studentMark->student_id = $model->student_id;
                            $studentMark->student_user_id = $queryVedomst->student_user_id;
                            $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                            $studentMark->edu_semestr_subject_id = $subject->id;
                            $studentMark->subject_id = $subject->subject_id;
                            $studentMark->edu_plan_id = $model->edu_plan_id;
                            $studentMark->edu_semestr_id = $model->edu_semestr_id;
                            $studentMark->faculty_id = $model->faculty_id;
                            $studentMark->direction_id = $model->direction_id;
                            $studentMark->semestr_id = $model->semestr_id;
                            $studentMark->course_id = $isStudentSubject->course_id;
                            $studentMark->vedomst = 1;
                            $studentMark->student_semestr_subject_vedomst_id = $queryVedomst->id;
                            if (!$studentMark->validate()) {
                                $errors[] = ['Student Mark Validate Errors'];
                            } else {
                                $studentMark->save(false);
                            }
                        } else {
                            $qStudentMark->group_id = $queryVedomst->group_id;
                            $qStudentMark->save(false);
                        }
                    }
                }
            }
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        }
        return ['is_ok' => false , 'errors' => $errors];
    }






    public function actionDel1()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $errors = [];

        $students = Student::find()
            ->where(['is_deleted' => 0 , 'status' => 10])
            ->all();
        foreach ($students as $student) {
            $get = StudentGroup::findOne([
                'student_id' => $student->id,
            ]);
            if (!$get) {
                $group = Group::findOne($student->group_id);
                if ($group) {
                    $eduSemestrs = EduSemestr::find()
                        ->where(['edu_plan_id' => $group->edu_plan_id , 'is_deleted' => 0])
                        ->orderBy('semestr_id asc')
                        ->all();
                    foreach ($eduSemestrs as $eduSemestr) {
                        $new = new StudentGroup();
                        $new->student_id = $student->id;
                        $new->group_id = $group->id;
                        $new->edu_year_id = $eduSemestr->edu_year_id;
                        $new->edu_plan_id = $eduSemestr->edu_plan_id;
                        $new->edu_semestr_id = $eduSemestr->id;
                        $new->edu_form_id = $eduSemestr->edu_form_id;
                        $new->semestr_id = $eduSemestr->semestr_id;
                        $new->course_id = $eduSemestr->course_id;
                        $new->faculty_id = $eduSemestr->faculty_id;
                        $new->direction_id = $eduSemestr->direction_id;
                        $new->save(false);
                        if ($eduSemestr->status == 1) {
                            $student->faculty_id = $eduSemestr->faculty_id;
                            $student->direction_id = $eduSemestr->direction_id;
                            $student->course_id = $eduSemestr->course_id;
                            $student->edu_year_id = $eduSemestr->edu_year_id;
                            $student->edu_type_id = $eduSemestr->edu_type_id;
                            $student->edu_form_id = $eduSemestr->edu_form_id;
                            $student->edu_lang_id = $group->language_id;
                            $student->edu_plan_id = $group->edu_plan_id;
                            $student->is_contract = 1;
                            $student->save(false);
                            break;
                        }
                    }
                }
            }
        }

        $transaction->commit();
        dd(232323);
    }

    public function actionStudentsImport3()
    {
        $errors = [];
        $inputFileName = __DIR__ . '/excels/newstudent (2).xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $i = 0;
        foreach ($data as $dataOne) {
            if (isset($dataOne[1])) {

                $group = Group::findOne(['unical_name' =>  $dataOne[1]]);
                $role = 'student';
                if (isset($group)) {

                    $model = new \api\resources\User();
                    $user = self::studentLogin();
                    $model->username = $user['username'];
                    $model->email= $user['email'];
                    $password = _passwordMK();
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                    $model->auth_key = \Yii::$app->security->generateRandomString(20);
                    $model->password_reset_token = null;
                    $model->access_token = \Yii::$app->security->generateRandomString();
                    $model->access_token_time = time();
                    $model->status = 10;
                    $model->save(false);
//
                    $model->savePassword($password, $model->id);

                    $profile = new Profile();
                    $profile->user_id = $model->id;
                    $profile->passport_pin = $dataOne[0];
                    $profile->save(false);

                    $student = new Student();
                    $student->group_id = $group->id;
                    $student->user_id = $model->id;
                    $student->type = 1;
                    $student->status = 10;
                    $student->save(false);

                    $auth = \Yii::$app->authManager;
                    $authorRole = $auth->getRole($role);
                    $auth->assign($authorRole, $model->id);

                } else {
                    $errors[] = $dataOne[1];
                }
                $i++;
                echo $i."\n";
            } else {
                break;
            }
        }

        dd($errors);

    }

    public static function studentLogin() {
        $result = [];
        $std = \api\resources\User::find()->orderBy(['id' => SORT_DESC])->one();
        if ($std) {
            $count = $std->id + 10000 + 1;
        } else {
            $count = 10000 + 1;
        }

        $result['username'] = 'sarbon-std-' . $count;
        if (!(isset($post['email']))) {
            $result['email'] = 'sarbon-std-' . $count . '@sarbon.uz';
        }
        return $result;
    }

    public function actionGetData()
    {
        $url = 'https://subsidiya.idm.uz/api/applicant/get-photo';

        $b = 0;
        $profiles = Profile::find()->where(['passport_number' => null])->all();
        foreach ($profiles as $profile) {

            if ($profile->passport_pin != null) {
                $data = json_encode([
                    'pinfl' => $profile->passport_pin
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
//                $photoBase64 = $response['data']['photo'] ?? null;
                $image = null;

//                if ($photoBase64) {
//                    // Rasmni dekodlash
//                    $photoData = base64_decode($photoBase64);
//
//                    if (!file_exists(\Yii::getAlias('@api/web/storage/std_image'))) {
//                        mkdir(\Yii::getAlias('@api/web/storage/std_image'), 0777, true);
//                    }
//
//                    // Saqlash uchun fayl nomini va yo‘lini aniqlash
//                    $fileName = $profile->passport_pin.'_ik.jpg'; // Fayl nomini kerakli tarzda o'zgartirishingiz mumkin
//                    $filePath = \Yii::getAlias('@api/web/storage/std_image/') . $fileName;
//                    $image = 'storage/std_image/'.$fileName;
//
//                    // Faylni papkaga saqlash
//                    file_put_contents($filePath, $photoData);
//
//                    echo $b++."\n";
//                }

                $pin = $response['data']['pinfl'] ?? null;
                $seria = $response['data']['docSeria'] ?? null;
                $number = $response['data']['docNumber'] ?? null;
                $last_name = $response['data']['surnameLatin'] ?? null;
                $first_name = $response['data']['nameLatin'] ?? null;
                $middle_name = $response['data']['patronymLatin'] ?? null;
                $birthday = $response['data']['birthDate'] ?? null;
                $b_date = $response['data']['docDateBegin'] ?? null;
                $e_date = $response['data']['docDateEnd'] ?? null;
                $given_by = $response['data']['docGivePlace'] ?? null;
                $jins = $response['data']['sex'] ?? null;

                $profile->first_name = $first_name;
                $profile->last_name = $last_name;
                $profile->middle_name = $middle_name;
                $profile->birthday = $birthday;
                $profile->passport_given_date = $b_date;
                $profile->passport_issued_date = $e_date;
                $profile->passport_given_by = $given_by;
                $profile->gender = $jins;
                $profile->passport_serial = $seria;
                $profile->passport_number = $number;
                $profile->passport_pin = $pin;
                $profile->image = $image;
                $profile->update(false);
            }
        }
    }


    public function actionIk()
    {
        $url = 'https://subsidiya.idm.uz/api/applicant/get-photo';

        $data = json_encode([
            'pinfl' => 52111045840018
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

        $photoBase64 = $response['data']['photo'] ?? null;

        if ($photoBase64) {
            // Rasmni dekodlash
            $photoData = base64_decode($photoBase64);

            if (!file_exists(\Yii::getAlias('@api/web/storage/std_image'))) {
                mkdir(\Yii::getAlias('@api/web/storage/std_image'), 0777, true);
            }

            // Saqlash uchun fayl nomini va yo‘lini aniqlash
            $fileName =  '60210056530017' . '__ik.jpg'; // Fayl nomini kerakli tarzda o'zgartirishingiz mumkin
            $filePath = \Yii::getAlias('@api/web/storage/std_image/') . $fileName;

            // Faylni papkaga saqlash
            file_put_contents($filePath, $photoData);
        }

        $pin = $photoBase64 = $response['data']['pinfl'];
        $seria = $photoBase64 = $response['data']['docSeria'];
        $number = $photoBase64 = $response['data']['docNumber'];
        $last_name = $photoBase64 = $response['data']['surnameLatin'];
        $first_name = $photoBase64 = $response['data']['nameLatin'];
        $middle_name = $photoBase64 = $response['data']['patronymLatin'];
        $birthday = $photoBase64 = $response['data']['birthDate'];
        $b_date = $photoBase64 = $response['data']['docDateBegin'];
        $e_date = $photoBase64 = $response['data']['docDateEnd'];
        $given_by = $photoBase64 = $response['data']['docGivePlace'];
        $jins = $photoBase64 = $response['data']['sex'];
    }

}