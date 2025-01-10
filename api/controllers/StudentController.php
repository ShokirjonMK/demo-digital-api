<?php

namespace api\controllers;

use api\components\HemisMK;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubject;
use common\models\model\StudentAttend;
use common\models\model\TimeTable1;
use common\models\model\TimetableAttend;
use common\models\model\TimeTableGroup;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Yii;
use api\resources\StudentUser;
use api\resources\User;

use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentExport;
use common\models\model\StudentPinn;
use common\models\model\StudentTimeOption;
use Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class  StudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Student';

    public function actions()
    {
        return [];
    }

    public $setFirstRecordAsKeys = true;

    public $getOnlySheet;

    public $leaveRecordByIndex = [];

    public $table_name = 'student';

    public $controller_name = 'Student';

    public function executeArrayLabel($sheetData)
    {
        $keys = ArrayHelper::remove($sheetData, '1');

        $new_data = [];

        foreach ($sheetData as $values) {
            $new_data[] = array_combine($keys, $values);
        }

        return $new_data;
    }

    public function actionStudent11($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if (!searchInArray($student->faculty_id, $t['UserAccess'])) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        $result = StudentUser::studentGroupUpdate($student, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionImport($lang)
    {
        $data = [];
        $errorAll = [];

        $post = Yii::$app->request->post();
        $file = UploadedFile::getInstancesByName('fff');
        if (!$file) {
            return $this->response(0, _e('Excel file required'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // $transaction = Yii::$app->db->beginTransaction();
        try {
            $inputFileType = IOFactory::identify($file[0]->tempName);
            $objReader = IOFactory::createReader($inputFileType);

            $objectPhpExcel = $objReader->load($file[0]->tempName);;

            $sheetDatas = [];

            $sheetDatas = $objectPhpExcel->getActiveSheet()->toArray(null, true, true, true);

            if ($this->setFirstRecordAsKeys) {
                $sheetDatas = $this->executeArrayLabel($sheetDatas);
            }

            if (!empty($this->getOnlyRecordByIndex)) {
                $sheetDatas = $this->executeGetOnlyRecords($sheetDatas, $this->getOnlyRecordByIndex);
            }
            if (!empty($this->leaveRecordByIndex)) {
                $sheetDatas = $this->executeLeaveRecords($sheetDatas, $this->leaveRecordByIndex);
            }

            foreach ($sheetDatas as $post) {
                /** */
                // $post = Yii::$app->request->post();
                if (isRole('tutor')) {
                    $post['tutor_id'] = current_user_id();
                }
                $post['role'] = 'student';
                $post['status'] = 10;

                $post['passport_pin'] = (int)$post['passport_pin'];
                $post['passport_number'] = (int)$post['passport_number'];
                $post['phone'] = (string)$post['phone'];
                $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));
                $post['passport_given_date'] = date('Y-m-d', strtotime($post['passport_given_date']));

                // $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));
                // $post['birthday'] = date('Y-m-d', strtotime($post['birthday']));


                $hasProfile = Profile::findOne(['passport_pin' => $post['passport_pin']]);
                // dd("asd");
                if ($hasProfile) {
                    $model = User::findOne(['id' => $hasProfile->user_id]);
                    $student = Student::findOne(['user_id' => $hasProfile->user_id]);

                    // $this->load($model, $post);
                    $this->load($hasProfile, $post);
                    if (!$student) {
                        $student = new Student();
                    }
                    $this->load($student, $post);
                    $data[] = [$model, $student, $hasProfile];
                    if ($model) {
                        $result = StudentUser::updateItem($model, $hasProfile, $student, $post);
                        // $errorAll[$post['passport_pin']] = $data;
                    } else {
                        $errorAll[$post['passport_pin']] = _e('There is a Profile but User not found!');
                    }
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

                    $post['username'] = 'utas_std_' . $count;
                    $post['email'] = 'utas_std_' . $count . '@utas.uz';
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

            return $errorAll;

            if (count($errorAll) > 0) {
                return $errorAll;
            }
            return $data;
            return $sheetDatas;
        } catch (Exception $e) {
            // $transaction->rollBack();
        }
    }

    /*  public function actionRead($lang)
    {
        $data = [];
        $post = Yii::$app->request->post();
        $file = UploadedFile::getInstancesByName('fff');
        $dd = [];
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $inputFileType = IOFactory::identify($file[0]->tempName);
            $objReader = IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($file[0]->tempName);

            $dataExcel = $objPHPExcel->getActiveSheet()->toArray();
            $k = 0;
            $t = true;
            foreach ($dataExcel as $key => $row) {

                               $dd['key'][] = $key;
                               $dd['count($row)'][] = count($row);
                for ($i = 0; $i < count($row); $i++) {
                    if ($k == 0) {
                        $dd['header'][] = $row[$i];

                    } else {
                        $dd['body'][] = $row[$i];
                    }

                }
                $k++;
            }
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        var_dump($dd, $data);
        die();
    } */

    public function actionByPinfl($pinfl)
    {
        $model = new StudentPinn();
        $query = $model->find()
            // ->with(['profile'])
            // ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')// ->groupBy('student.id')
        ;
        $query->andFilterWhere([
            'profile.passport_pin' => $pinfl
        ]);

        $data = $query->one();
        if ($data) {
            return $this->response(1, _e('Success'), $data);
        }
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    }

    public function actionGet($pinfl)
    {
        $hemis = new HemisMK();

        $data = $hemis->getHemis($pinfl);

        // return $data->success;
        // return $data;
        if ($data->success) {
            if (isset($data->data))
                return $this->response(1, _e('Success'), $data->data);

            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        } else {
            return $this->response(0, _e($data->data), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionIndex($lang)
    {
        /*********/
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->join('INNER JOIN', 'users', 'users.id = student.user_id');

        $attend = Yii::$app->request->get('attend_sort');
        if ($attend == 1) {
            $subquery = (new Query())
                ->select(['student_id', 'COUNT(*) AS student_count'])
                ->from('timetable_attend')
                ->groupBy('student_id')
                ->orderBy(['student_count' => SORT_DESC]);

            $query->leftJoin(['subquery' => $subquery], 'student.id = subquery.student_id')
                ->orderBy(['subquery.student_count' => SORT_DESC]);
        }


        if (isRole('dean')) {
            $dean = get_dean();
            if ($dean) {
                $query->andWhere(['faculty_id' => $dean->id]);
            } else {
                $query->andWhere(['faculty_id' => -1]);
            }
        } elseif (isRole('dean_deputy')) {
            $dean_deputy = get_dean_deputy();
            if ($dean_deputy) {
                $query->andWhere(['faculty_id' => $dean_deputy->id]);
            } else {
                $query->andWhere(['faculty_id' => -1]);
            }
        }

        if (isRole('tutor')) {
            $query = $query->andWhere([
                'tutor_id' => current_user_id()
            ]);
        }

        //  Filter from Profile
        $profile = new Profile();
        $user = new User();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $user->attributes())) {
                    $query = $query->andFilterWhere(['users.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        if (isset($queryfilter)) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if (isset($queryfilter)) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $profile->attributes())) {
                        $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                    }
                    if (in_array($attributeq, $user->attributes())) {
                        $query = $query->andFilterWhere(['like', 'users.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionMissedHours($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0 , 'student.status' => 10])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');

        $eduSemestrSubjectId = Yii::$app->request->get('edu_semestr_subject_id');
        if ($eduSemestrSubjectId !== null) {
            $eduSemestrSubject = EduSemestrSubject::findOne([
                'id' => $eduSemestrSubjectId,
                'status' => 1,
                'is_deleted' => 0
            ]);

            if ($eduSemestrSubject !== null) {
                $allHours = $eduSemestrSubject->hours;

                $subquery = (new Query())
                    ->select(['student_id', 'COUNT(*) AS student_count'])
                    ->from('timetable_attend')
                    ->where([
                        'subject_id' => $eduSemestrSubject->subject_id,
                        'edu_semestr_id' => $eduSemestrSubject->edu_semestr_id,
                        'reason' => 0,
                        'is_deleted' => 0
                    ])
                    ->groupBy('student_id')
                    ->orderBy(['student_count' => SORT_DESC]);

                $result = $subquery->all();

                $fromPercent = (int) Yii::$app->request->get('from_percent', 0);
                $toPercent = (int) Yii::$app->request->get('to_percent', 100);
                $dataStudent = [];
                foreach ($result as $row) {
                    $studentId = $row['student_id'];
                    $studentCount = $row['student_count'];
                    $percentage = ($studentCount / $allHours) * 100;

                    if ($percentage >= $fromPercent && $percentage <= $toPercent) {
                        $dataStudent[] = $studentId;
                    }
                }
                $query = $query->andWhere(['student.id' => $dataStudent]);
            }
        }

        if (isRole('dean')) {
            $dean = get_dean();
            if ($dean) {
                $query->andWhere(['student.faculty_id' => $dean->id]);
            } else {
                $query->andWhere(['student.faculty_id' => -1]);
            }
        } elseif (isRole('dean_deputy')) {
            $dean_deputy = get_dean_deputy();
            if ($dean_deputy) {
                $query->andWhere(['student.faculty_id' => $dean_deputy->id]);
            } else {
                $query->andWhere(['student.faculty_id' => -1]);
            }
        }

        if (isRole('tutor')) {
            $query = $query->andWhere([
                'student.tutor_id' => current_user_id()
            ]);
        }

        //  Filter from Profile
        $profile = new Profile();
        $user = new User();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $user->attributes())) {
                    $query = $query->andFilterWhere(['users.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
                if (in_array($attributeq, $user->attributes())) {
                    $query = $query->andFilterWhere(['like', 'users.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionExport($lang)
    {
        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => [
                'Users' => [
                    'class' => 'codemix\excelexport\ActiveExcelSheet',
                    'query' => Student::find()->select('passport_pin'),
                ]
            ]
        ]);

        $url = \Yii::getAlias('@api/web/export/export1.xlsx');
        $file->saveAs($url);
        return $file;


        /*********/
        $model = new StudentExport();

        $query = $model->find()
            ->with(['profile', 'eduLang', 'eduPlan'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')// ->groupBy('student.id')
        ;

        // return $model->tableName();
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'faculty_id' => -1
            ]);
        }

        /*  is Role check  */
        if (isRole('tutor')) {
            $query = $query->andWhere([
                'tutor_id' => current_user_id()
            ]);
        }

        //  Filter from Profile
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        $data = $data->asArray();

        /** Excel Export */

        // return $model->attributes();
        return $data;
        $mySpreadsheet = new Spreadsheet();
        $mySpreadsheet->removeSheetByIndex(0);

        // Create "Sheet 1" tab as the first worksheet.
        // https://phpspreadsheet.readthedocs.io/en/latest/topics/worksheets/adding-a-new-worksheet
        $worksheet1 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($mySpreadsheet, "Students");
        $mySpreadsheet->addSheet($worksheet1, 0);


        $sheet1Data = [
            ["First Name", "Last Name", "Date of Birth"],
            ['Britney', "Spears", "02-12-1981"],
            ['Michael', "Jackson", "29-08-1958"],
            ['Christina', "Aguilera", "18-12-1980"],
        ];

        // Sheet 2 contains list of ferrari cars and when they were manufactured.


        $worksheet1->fromArray($sheet1Data);


        // Change the widths of the columns to be appropriately large for the content in them.
        // https://stackoverflow.com/questions/62203260/php-spreadsheet-cant-find-the-function-to-auto-size-column-width
        $worksheets = [$worksheet1];

        foreach ($worksheets as $worksheet) {
            foreach ($worksheet->getColumnIterator() as $column) {
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        // Save to file.
        $writer = new Xlsx($mySpreadsheet);
        $writer->save('output.xlsx');

        if (!file_exists(STORAGE_PATH . 'student_export')) {
            mkdir(STORAGE_PATH . 'student_export', 0777, true);
        }

        $fileName = time() . '_std.xlsx';

        $miniUrl = 'student_export/' . $fileName;
        $url = STORAGE_PATH . $miniUrl;
        $writer->save($url, false);
        $excel_url = "storage/" . $miniUrl;


        return $this->response(1, _e('Success'), $excel_url);
    }

    public function actionType($lang)
    {
        $post = Yii::$app->request->post();

        $result = StudentUser::studentType($post);

        if (!is_array($result)) {
            return $this->response(1, _e('Student type update.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionTutor($lang)
    {
        $post = Yii::$app->request->post();

        $result = StudentUser::studentTutor($post);

        if (!is_array($result)) {
            return $this->response(1, _e('A tutor has been attached to the student.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID, 2);
        if ($t['status'] == 1) {
            $post['faculty_id'] = $t['UserAccess'];
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        /*  is Self  */

        if (isRole('tutor')) {
            $post['tutor_id'] = current_user_id();
        }
        $post['role'] = 'student';
        $model = new User();
        $profile = new Profile();
        $student = new Student();

//        $users = Student::find()->count();
//        $std = Student::find()->orderBy(['id' => SORT_DESC])->one();
        $std = User::find()->orderBy(['id' => SORT_DESC])->one();
//        $count = $users + 10001;
        if ($std) {
            $count = $std->id + 10000 + 1;
        } else {
            $count = 10000 + 1;
        }

        $post['username'] = 'sarbon-std-' . $count;
        if (!(isset($post['email']))) {
            $post['email'] = 'sarbon-std' . $count . '@sarbon.uz';
        } else {
            $userEmail = User::findOne([
                'email' => $post['email']
            ]);
            if (isset($userEmail)) {
                $error[] = ['email' => [_e($post['email'] . ' You cannot enter this email')]];
                return $this->response(0, _e('Email has already been registered'), null, $error, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::createItem($model, $profile, $student, $post);
        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully created.'), $data, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStatisticAttend()
    {
        $query = StudentAttend::find()
            ->select(['COUNT(*) AS count','student_id'])
            ->where(['status' => 1, 'is_deleted' => 0])
            ->groupBy('student_id')
            ->orderBy('count DESC')
            ->asArray()
            ->all();
        return $query;
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if (!searchInArray($student->faculty_id, $t['UserAccess'])) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }


        /*  is Self  */
        $post['role'] = 'student';

        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model = User::findOne(['id' => $student->user_id]);
        $profile = Profile::findOne(['user_id' => $student->user_id]);

        if (isset($post['email'])) {
            $userEmail = User::findOne([
                'email' => $post['email']
            ]);
            if (isset($userEmail)) {
                $error[] = ['email' => [_e($post['email'] . ' You cannot enter this email')]];
                return $this->response(0, _e('Email has already been registered'), null, $error, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        if (isset($post['type']) && ($post['type'] != 1 && $post['type'] != 2)) {
            unset($post['type']);
        }

        $StudentGroup_group_id = $student->group_id;
//        $StudentType = $student->type;
        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);

        $result = StudentUser::updateItem($model, $profile, $student, $post, $StudentGroup_group_id);

        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

//        dd($result);
        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $data, null, ResponseStatus::OK);
        } else {
//            dd(11111);
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess'][0]) {
                return $this->response(0, _e('This information will not be provided to you.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess'][0]) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $result = StudentUser::deleteItem($id);

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        /*
        $model = StudentUser::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $user = User::findOne($model->user_id);
            $user->status = User::STATUS_BANNED;
            $user->save(false);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e('Student succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        */
    }

    public function actionMe($lang)
    {
        $student = Student::findOne(['user_id' => current_user_id()]);

        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $student, null, ResponseStatus::OK);
    }

}
