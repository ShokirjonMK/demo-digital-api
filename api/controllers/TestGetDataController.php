<?php

namespace api\controllers;

use api\components\HemisMK;
use api\components\MipService;
use api\components\MipServiceMK;
use api\components\MipTokenGen;
use api\components\PersonDataHelper;

use base\ResponseStatus;
use common\models\model\Attend;
use common\models\model\Chichqoq;
use common\models\model\LoginHistory;
use common\models\model\Profile;
use common\models\model\StudentAttend;
use common\models\model\SubjectContent;



use Yii;
use api\resources\StudentUser;
use api\resources\User;

use common\models\model\Faculty;
use common\models\model\KuvondikMasofaviy;
use common\models\model\Student;
use common\models\model\StudentExport;
use common\models\model\StudentPinn;
use common\models\model\SubjectCategory;
use common\models\model\SubjectSillabus;
use Exception;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

class TestGetDataController extends ApiActiveController
{
    public $modelClass = 'api\resources\TestGetData';

    public function actions()
    {
        return [];
    }

    public function actionHemis($pinfl)
    {
        $hemis = new HemisMK();

        $data = $hemis->getHemis($pinfl);
        // return $data;
        if ($data->success) {
            return $this->response(1, _e('Success'), $data->data);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $data->data, ResponseStatus::FORBIDDEN);
        }
    }

    public function actionIndex()
    {

        $data = [];
        $profiles = Profile::find()
            ->andWhere(['checked_full' => 0])
            ->andWhere(['is not', 'passport_pin', null])
            ->limit(1000)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach ($profiles as $profile) {

            // $data = $hemis->getHemis($profile->passport_pin);
            // $profile->checked = 1;
            $mip = MipServiceMK::corrent($profile);
            $data[] = $mip;
            // $profile->save(false);
        }

        return $this->response(1, _e('Success'), $data);

        $data = [];

        $students = Student::find()
            ->where(['status' => 1, 'is_deleted' => 0]);

        $students->andFilterWhere([
            'in', 'student.edu_plan_id',
            [137, 62, 17, 135, 88, 60, 136, 61, 58, 138, 64, 25, 141, 140, 134, 63, 20, 158, 131, 55, 160, 132]
        ]);



        // ->limit(1000)
        // ->offset(8000)
        // ->orderBy(['id' => SORT_DESC])

        $students->all();

        $data = [];
        foreach ($students as $student) {
            $data[$student->id];
        }

        return $this->response(1, _e('Success'), $data);
    }
    // public function actionIndexasd()
    // {
    //     $data = [];

    //     $profiles = Profile::find()
    //         // ->where(['checked' => 0])
    //         // ->where(['checked_full' => 1])
    //         // ->andWhere(['checked_full' => 0])
    //         ->andWhere(['is not', 'passport_pin', null])
    //         ->andWhere(['is not', 'passport_given_date', null])
    //         // ->limit(1000)
    //         // ->offset(8000)
    //         // ->orderBy(['id' => SORT_DESC])
    //         ->all();

    //     $fileFolder = STORAGE_PATH  . '1111/';
    //     foreach ($profiles as $profile) {
    //         $oldFilePath = $fileFolder . $profile->passport_pin . '.png';
    //         $newFileName = $profile->last_name . "+" . $profile->first_name . "_" . $profile->passport_pin;

    //         $newFolderPath = STORAGE_PATH  . 'turniket_tahlandiiii1111111/';

    //         if (file_exists($oldFilePath)) {
    //             // Get the file extension
    //             $fileExtension = pathinfo($oldFilePath, PATHINFO_EXTENSION);

    //             // Generate the new file path
    //             $newFilePath = $newFolderPath . $newFileName . '.' . $fileExtension;

    //             // Create the new folder if it doesn't exist
    //             FileHelper::createDirectory($newFolderPath);

    //             // Rename the file
    //             rename($oldFilePath, $newFilePath);
    //             $data[] = [$profile->passport_pin => true];
    //         } else {
    //             $data[] = [$profile->passport_pin => false];

    //             // fayl yo'q, bundan so'ng qandaydir muvaffaqiyatsizlik xabarini ko'rsatishingiz mumkin
    //         }
    //     }

    //     return $this->response(1, _e('Success'), $data);
    // }


    public function actionIndexSubjectSillabusAuditoryTime()
    {
        // $subjectSillabus = SubjectSillabus::find()->all();

        // foreach ($subjectSillabus as $model) {
        //     $auditory_time = 0;
        //     foreach (json_decode($model->edu_semestr_subject_category_times)
        //         as $edu_semestr_subject_category_times_key => $edu_semestr_subject_category_times_value) {
        //         if (SubjectCategory::find()
        //             ->where([
        //                 'id' => $edu_semestr_subject_category_times_key,
        //                 'type' => 1
        //             ])
        //             ->exists()
        //         ) {
        //             $auditory_time += $edu_semestr_subject_category_times_value;
        //         }
        //     }
        //     $model->auditory_time = $auditory_time;
        //     $model->save();
        // }

        return "no thing";
    }

    public function actionIndex111($i)
    {
        //     $errors = [];
        //     $soni = $i * 500;

        //     // return $this->response(1, _e('Success'), $soni);

        //     $attends = Attend::find()
        //         ->limit(500)->offset($soni)->all();


        //     foreach ($attends as $one) {


        //         foreach ($one->student_ids as $student_id) {
        //             /** new Student Attent here */

        //             /** Checking student is really choos this time table */

        //             /** Checking student is really choos this time table */
        //             $newStudentAttend = new StudentAttend();
        //             $newStudentAttend->student_id = $student_id;
        //             $newStudentAttend->attend_id = $one->id;
        //             $newStudentAttend->time_table_id = $one->time_table_id;
        //             $newStudentAttend->subject_id = $one->subject_id;
        //             $newStudentAttend->date = $one->date;
        //             $newStudentAttend->subject_category_id = $one->subject_category_id;
        //             $newStudentAttend->edu_year_id = $one->edu_year_id;
        //             $newStudentAttend->time_option_id = $one->time_option_id;
        //             $newStudentAttend->edu_semestr_id = $one->edu_semestr_id;
        //             $newStudentAttend->faculty_id = $one->faculty_id;
        //             $newStudentAttend->course_id = $one->timeTable->course_id;
        //             $newStudentAttend->edu_plan_id = $one->edu_plan_id;
        //             $newStudentAttend->type = $one->type;
        //             $newStudentAttend->semestr_id = $one->eduSemestr->semestr_id;

        //             $newStudentAttend->updated_by = $one->updated_by;
        //             $newStudentAttend->created_by = $one->created_by;
        //             $newStudentAttend->updated_at = $one->updated_at;
        //             $newStudentAttend->created_at = $one->created_at;

        //             // return $this->response(1, _e('Success'), $newStudentAttend->created_at);

        //             // $newStudentAttend->reason = $one->reason;
        //             if (!$newStudentAttend->save()) {
        //                 $errors[] = [$student_id => $newStudentAttend->errors];
        //             }
        //             /** new Student Attent here */
        //         }
        //     }

        //     return $this->response(1, _e('Success'), $errors);
    }



    /*  public function actionChich($passport = null, $jshir = null)
    {
        $chich = Chichqoq::find()->all();
        $errors = [];
        foreach ($chich as $ch) {
            $sub_content = SubjectContent::findAll(['subject_topic_id' => $ch->a1]);

            foreach ($sub_content as $s_c) {
                $new = new SubjectContent();

                $new->content = $s_c->content;
                $new->type = $s_c->type;
                $new->subject_id = $ch->a4;
                $new->user_id = $s_c->user_id;
                $new->subject_topic_id = $ch->a2;
                $new->teacher_access_id = $s_c->teacher_access_id;
                $new->description = $s_c->description;
                $new->file_url = $s_c->file_url;
                $new->order = $s_c->order;
                $new->status = $s_c->status;
                $new->created_at = $s_c->created_at;
                $new->updated_at = $s_c->updated_at;
                $new->created_by = $s_c->created_by;
                $new->updated_by = $s_c->updated_by;
                $new->is_deleted = $s_c->is_deleted;

                if (!$new->save()) {
                    $errors[] = $new->errors;
                }
            }
        }

        return $this->response(1, _e('Success'), $errors);
    } */

    public function actionViewss()
    {

        //     $profiles = Profile::find()
        //         ->where(['checked' => 0])
        //         // ->andWhere(['checked_full' => 0])
        //         ->andWhere(['is not', 'passport_pin', null])
        //         ->andWhere(['is not', 'passport_given_date', null])
        //         ->limit(10)
        //         // ->offset(0)
        //         ->orderBy(['id' => SORT_DESC])
        //         ->all();

        //     foreach ($profiles as $profile) {
        //         $hemis = new HemisMK();

        //         $data = $hemis->getHemis($profile->passport_pin);
        //         $profile->checked = 1;
        //         $mip = MipServiceMK::corrent($profile);
        //         $data[] = $mip;
        //         $profile->save(false);
        //     }

        //     return $this->response(1, _e('Success'), $data);
    }


    // public function actionProfileMipss($passport = null, $jshir = null)
    public function actionViewfreeMahalladan()
    {

        //////  Profile get from MIP
        $data = [];
        $profiles = Profile::find()
            ->where(['checked' => 0])
            // ->where(['checked_full' => 1])
            ->andWhere(['checked_full' => 0])
            ->andWhere(['is not', 'birthday', null])
            ->andWhere(['is not', 'passport_number', null])
            ->andWhere(['is not', 'passport_seria', null])
            ->limit(1000)
            // ->offset(0)
            // ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach ($profiles as $profile) {

            $profile->checked = 1;
            $mip = MipServiceMK::freeMahalladan($profile);
            $data[] = $mip;
            $profile->save(false);
        }

        return $this->response(1, _e('Success'), $data);
    }

    // public function actionProfileMips($passport = null, $jshir = null)
    public function actionView($passport = null, $jshir = null)
    {
        //////  Profile get from MIP
        $data = [];
        $profiles = Profile::find()
            ->where(['checked' => 0])
            // ->where(['checked_full' => 1])
            ->andWhere(['checked_full' => 0])
            ->andWhere(['is not', 'passport_pin', null])
            ->andWhere(['is not', 'passport_given_date', null])
            ->limit(1000)
            // ->offset(0)
            // ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach ($profiles as $profile) {

            $profile->checked = 1;
            $mip = MipServiceMK::corrent($profile);
            $data[] = $mip;
            $profile->save(false);
        }

        return $this->response(1, _e('Success'), $data);


        return 0;

        HemisMK::refreshToken();
        $mip = MipServiceMK::getData(61801045840029, "2021-01-13");
        if ($mip['status']) {
            return $this->response(1, _e('Success'), $mip['data']);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $mip['error'], ResponseStatus::UPROCESSABLE_ENTITY);
        }


        $pin = "61801045840029";
        $document_issue_date =  "2021-01-13";

        // Define the Base64 value you need to save as an image
        $base64string = "/9j/4AAQF9\nPSn4FR4NLg+tID//2Q==";

        // $base64string = '';
        $uploadpath   = STORAGE_PATH  . 'base64/';
        if (!file_exists(STORAGE_PATH  . 'base64/')) {
            mkdir(STORAGE_PATH  . 'base64/', 0777, true);
        }


        $parts        = explode(
            ";base64,",
            $base64string
        );
        $imageparts   = explode("image/", @$parts[0]);
        // $imagetype    = $imageparts[1];
        $imagebase64  = base64_decode($base64string);
        $miniurl = uniqid() . '.png';
        $file = $uploadpath . $miniurl;

        file_put_contents($file, $imagebase64);

        return 'storage/base64/' . $miniurl;
        //    return LoginHistory::createItemLogin();

        // return getIpAddressData();

        // return 1;
        // $data = MipTokenGen::getToken();
        $pinpp = "60111016600035";
        $doc_give_date = "2017-09-28";

        $data = MipService::getPhotoService($pinpp, $doc_give_date);

        return $this->response(1, _e('Success'), $data);


        $mk = new MipService();


        // $xml = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date)); // where $xml_string is the XML data you'd like to use (a well-formatted XML string). If retrieving from an external source, you can use file_get_contents to retrieve the data and populate this variable.
        // $json = json_encode($xml); // convert the XML string to JSON
        // $array = json_decode($json, TRUE);


        /*  $xmlObject = simplexml_load_string($mk->getPhotoService($pinpp, $doc_give_date));

        //Encode the SimpleXMLElement object into a JSON string.
        $jsonString = json_encode($xmlObject);

        //Convert it back into an associative array for
        //the purposes of testing.
        $jsonArray = json_decode($jsonString, true);

        //var_dump out the $jsonArray, just so we can
        //see what the end result looks like
        return $jsonArray;


        return $array ; */

        $rrrr = $mk->getPhotoService($pinpp, $doc_give_date);

        return $rrrr;
        return simplexml_load_file($rrrr);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $model = new PersonDataHelper();
        //  $data = $model->services($jshir, $passport);
        $data = $model->services("30505985280023", "AA7231228");
        if (empty($data)) {
            return 'error-no';
        } else {
            return $data;
        }
    }

    public function actionStudentExportKuvondik()
    {
        $dataFormTable = KuvondikMasofaviy::find()->all();


        foreach ($dataFormTable as $dataOne) {


            $post = [];
            $result = true;
            $post['tutor_id'] = 609;

            $post['role'] = 'student';
            $post['status'] = 10;

            $post['passport_pin'] = $dataOne->passport_pin;
            $post['last_name'] = $dataOne->last_name;
            $post['first_name'] = $dataOne->first_name;
            $post['middle_name'] = $dataOne->middle_name;
            $post['citizenship_id'] = $dataOne->citizenship_id;
            $post['country_id'] = $dataOne->country_id;
            $post['nationality_id'] = $dataOne->nationality_id;
            $post['gender'] = $dataOne->gender;
            $post['birthday'] = $dataOne->birthday;
            $post['passport_given_date'] = $dataOne->passport_given_date;
            $post['course_id'] = $dataOne->course_id;
            $post['faculty_id'] = $dataOne->faculty_id;
            $post['direction_id'] = $dataOne->direction_id;
            $post['edu_year_id'] = $dataOne->edu_year_id;
            $post['edu_plan_id'] = $dataOne->edu_plan_id;
            $post['edu_type_id'] = $dataOne->edu_type_id;
            $post['edu_lang_id'] = $dataOne->edu_lang_id;
            $post['edu_form_id'] = $dataOne->edu_form_id;
            $post['is_contract'] = $dataOne->is_contract;
            $post['student_category_id'] = $dataOne->student_category_id;

            $post['passport_pin'] = (int)$post['passport_pin'];
            $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));
            $post['passport_given_date'] = date('Y-m-d', strtotime($post['passport_given_date']));

            // $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));
            // $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));

            $hasProfile = Profile::findOne(['passport_pin' => $post['passport_pin']]);
            // dd("asd");
            if ($hasProfile) {
                // $model = User::findOne(['id' => $hasProfile->user_id]);
                // $student = Student::findOne(['user_id' => $hasProfile->user_id]);

                // // $this->load($model, $post);
                // $this->load($hasProfile, $post);
                // if (!$student) {
                //     $student = new Student();
                // }
                // $this->load($student, $post);
                // $data[] = [$model, $student, $hasProfile];
                // if ($model) {
                //     $result = StudentUser::updateItem($model, $hasProfile, $student, $post);
                //     // $errorAll[$post['passport_pin']] = $data;
                // } else {
                //     $errorAll[$post['passport_pin']] = _e('There is a Profile but User not found!');
                // }
            } else {

                $model = new User();
                $profile = new Profile();
                $student = new Student();
                $users = Student::find()->count();
                $count = $users + 10001;
                $std = Student::find()->orderBy(['id' => SORT_DESC])->one();
                $count = $users + 10001;
                if ($std) {
                    $count = $std->id + 10001;
                }

                $post['username'] = 'tsul_std_' . $count;
                $post['email'] = 'tsul_std_' . $count . '@tsul.uz';
                $this->load($model, $post);
                $this->load($profile, $post);
                $this->load($student, $post);

                $result = StudentUser::createItemImport($model, $profile, $student, $post);
                // return 1112;
                if (is_array($result)) {
                    $errorAll[$post['passport_pin']] = $result;
                }



                $data[] = [$model, $student, $profile];
            }



            if (is_array($result)) {
                $errorAll[$post['passport_pin']] = $result;
            } else {
                // $errorAll[$post['passport_pin']] = $data;
            }
        }
    }
}
