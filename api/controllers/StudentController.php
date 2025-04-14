<?php

namespace api\controllers;

use api\components\HemisMK;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Yii;
use api\resources\StudentUser;
use api\resources\User;

use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\ProfileSelf;
use common\models\model\Student;
use common\models\model\StudentExport;
use common\models\model\StudentPinn;
use common\models\model\StudentTimeOption;
use common\models\model\Turniket;
use Exception;
use yii\caching\DbDependency;
use yii\db\Expression;
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
                    $post['tutor_id'] = Current_user_id();
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

    // public function actionRead($lang)
    // {
    //     $data = [];
    //     $post = Yii::$app->request->post();
    //     $file = UploadedFile::getInstancesByName('fff');
    //     $dd = [];
    //     $transaction = Yii::$app->db->beginTransaction();
    //     try {
    //         $inputFileType = IOFactory::identify($file[0]->tempName);
    //         $objReader = IOFactory::createReader($inputFileType);
    //         $objPHPExcel = $objReader->load($file[0]->tempName);

    //         $dataExcel = $objPHPExcel->getActiveSheet()->toArray();
    //         $k = 0;
    //         $t = true;
    //         foreach ($dataExcel as $key => $row) {

    //             $dd['key'][] = $key;
    //             $dd['count($row)'][] = count($row);
    //             for ($i = 0; $i < count($row); $i++) {
    //                 if ($k == 0) {
    //                     $dd['header'][] = $row[$i];
    //                 } else {
    //                     $dd['body'][] = $row[$i];
    //                 }
    //             }
    //             $k++;
    //         }
    //         $transaction->commit();
    //     } catch (Exception $e) {
    //         $transaction->rollBack();
    //     }
    //     var_dump($dd, $data);
    //     die();
    // }

    public function actionByPinfl($pinfl)
    {
        $model = new StudentPinn();
        $query = $model->find()
            // ->with(['profile'])
            // ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            // ->groupBy('student.id')
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


    /********
     *  SALOM SALOM
     */
    public function actionGet($pinfl)
    {
        $hemis = new HemisMK();

        $data = $hemis->getHemis($pinfl);

        // return $data->success;
        // return $data;
        if (isset($data->success)) {
            if (isset($data->data))
                return $this->response(1, _e('Success'), $data->data);

            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        } else {
            if (isset($data->data))
                return $this->response(0, _e($data->data), null, null, ResponseStatus::NOT_FOUND);

            return $this->response(0, _e('There is no connection with HEMIS.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIndex5541($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->innerJoin('profile', 'profile.user_id = student.user_id')
            ->innerJoin('users', 'users.id = student.user_id');

        // Role check
        if (isRole('tutor')) {
            $query->andWhere(['tutor_id' => current_user_id()]);
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query->andWhere(['faculty_id' => $t['UserAccess']->table_id]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere(['faculty_id' => -1]);
            }
        }

        // Filter from Profile and User
        $profile = new Profile();
        $user = new User();
        $filter = Yii::$app->request->get('filter');
        if ($filter) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $user->attributes())) {
                    $query->andFilterWhere(['users.' . $attribute => $id]);
                }
            }
        }

        // Filter-like
        $queryFilterLike = Yii::$app->request->get('filter-like');
        $queryFilterLike = json_decode(str_replace("'", "", $queryFilterLike), true);
        if ($queryFilterLike) {
            foreach ($queryFilterLike as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
                if (in_array($attributeq, $user->attributes())) {
                    $query->andFilterWhere(['like', 'users.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // Filter all
        $query = $this->filterAll($query, $model);

        // Sort
        $query = $this->sort($query);

        // Data caching
        $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student']));

        // Data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionIndex($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->innerJoin('profile', 'profile.user_id = student.user_id')
            ->innerJoin('users', 'users.id = student.user_id');


        if (isset($not_come)) {
            $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

            $query->join('LEFT JOIN', 'turniket', 'turniket.turniket_id = profile.turniket_id and turniket.date = :date and turniket.type = :type', [':date' => $date, ':type' => Turniket::TYPE_OQUV])
                ->andWhere(['turniket.turniket_id' => null]);
        }

        /*  is Self  */

        /*  is Role check  */
        if (isRole('tutor')) {
            $query = $query->andWhere([
                'tutor_id' => current_user_id(),
            ]);

            $query = $query->andWhere(['!=', $model->tableName() . '.status', 20]);
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    'faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
            }
        }

        //  Filter from Profile 
        $profile = new Profile();
        $user = new User();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        //  Filter from Profile 

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

        // Filter by last_in less than 10 days ago
        $days_no_login = Yii::$app->request->get('days_no_login');
        if ($days_no_login > 1) {
            $daysAgo = date('Y-m-d H:i:s', strtotime('-' . $days_no_login . ' days'));
            $query->andWhere(['<', 'profile.last_in', $daysAgo]);
        }


        $createdAt = Yii::$app->request->get('createdAt');
        if ($createdAt !== null) {
            $query->andWhere(['>', 'student.created_at', $createdAt]);
        }
        // sqlraw($query);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data caching
        // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student']));


        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionDeleted($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere([$model->tableName() . '.is_deleted' => 1])
            ->innerJoin('profile', 'profile.user_id = student.user_id')
            ->innerJoin('users', 'users.id = student.user_id');

        /*  is Self  */

        /*  is Role check  */
        if (isRole('tutor')) {
            $query = $query->andWhere([
                'tutor_id' => current_user_id()
            ]);
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    'faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
            }
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

        // Filter by last_in less than 10 days ago
        $days_no_login = Yii::$app->request->get('days_no_login');
        if ($days_no_login > 1) {
            $daysAgo = date('Y-m-d H:i:s', strtotime('-' . $days_no_login . ' days'));
            $query->andWhere(['<', 'profile.last_in', $daysAgo]);
        }


        $createdAt = Yii::$app->request->get('createdAt');
        if ($createdAt !== null) {
            $query->andWhere(['>', 'student.created_at', $createdAt]);
        }
        // sqlraw($query);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data caching
        // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student']));


        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionTimeTableProbmes($lang)
    {
        // Subquery
        $subQuery = (new Query())
            ->select([
                'student.id',
                'student.faculty_id',
                'student.edu_plan_id',
                'student.edu_lang_id',
                'student.course_id',
                '`profile`.last_name',
                '`profile`.first_name',
                '`profile`.middle_name',
                '`profile`.passport_pin',
                'count(*) as soni'
            ])
            ->from('student')
            ->leftJoin('student_time_table', 'student.id = student_time_table.student_id')
            ->leftJoin('profile', 'student.user_id = `profile`.user_id')
            // ->where(['in', 'student.course_id', [2, 3, 4]])
            ->andWhere(['student_time_table.archived' => 0])
            // ->andWhere(['<>', 'student.faculty_id', 5])
            ->andWhere(['<>', 'student.is_deleted', 1])
            ->groupBy([
                'student.id',
                'student.faculty_id',
                'student.edu_plan_id',
                'student.edu_lang_id',
                'student.course_id',
                '`profile`.last_name',
                '`profile`.first_name',
                '`profile`.middle_name',
                '`profile`.passport_pin'
            ]);

        // Main query
        $query = (new Query())
            ->from(['subquery' => $subQuery])
            ->where(
                new Expression(
                    "CASE
                WHEN course_id = 1 THEN soni <> 24
                WHEN course_id = 2 THEN soni <> 24
                WHEN course_id = 3 THEN soni <> 20
                WHEN course_id = 4 THEN soni <> 16
                ELSE 1 = 1
            END"
                )
            )
            ->orderBy(['course_id' => SORT_ASC]);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionTimeOptionNot($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->leftJoin('student_time_option', 'student.id = student_time_option.student_id')
            ->leftJoin('profile', 'student.user_id = profile.user_id')
            ->leftJoin('translate AS fac', 'fac.model_id = student.faculty_id AND fac.table_name = "faculty" AND fac.language = "uz"')
            ->leftJoin('translate AS eduplan', 'eduplan.model_id = student.edu_plan_id AND eduplan.table_name = "edu_plan" AND eduplan.language = "uz"')
            ->where(['IS', 'student_time_option.student_id', new \yii\db\Expression('NULL')])
            // ->andWhere(['IN', 'student.edu_plan_id', [16, 21, 26, 57, 17, 20, 25, 58, 60, 61, 62, 63, 64, 88]])
            ->andWhere(['<>', 'student.is_deleted', 1])
            ->andWhere(['<>', 'student.course_id', 9]);
        // ->andWhere(['<>', 'student.faculty_id', 5]);

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                $this->table_name . '.faculty_id' => $t['UserAccess']->table_id
            ]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                $this->table_name . '.faculty_id' => -1
            ]);
        }

        /*  is Role check  */
        if (isRole('tutor')) {
            $query = $query->andWhere([
                $this->table_name . '.tutor_id' => current_user_id()
            ]);
        }

        //  Filter from Profile 
        $profile = new Profile();
        $user = new User();
        $student_time_option = new StudentTimeOption();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $user->attributes())) {
                    $query = $query->andFilterWhere(['users.' . $attribute => $id]);
                }
                if (in_array($attribute, $student_time_option->attributes())) {
                    $query = $query->andFilterWhere(['student_time_option.' . $attribute => $id]);
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

        // // Filter by last_in less than 10 days ago
        // $days_no_login = Yii::$app->request->get('days_no_login');
        // if ($days_no_login > 1) {
        //     $daysAgo = date('Y-m-d H:i:s', strtotime('-' . $days_no_login . ' days'));
        //     $query->andWhere(['<', 'profile.last_in', $daysAgo]);
        // }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // sqlraw($query);
        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionExport($lang)
    {
        /*********/
        $model = new StudentExport();

        $query = $model->find()
            ->with(['profile', 'eduLang', 'eduPlan'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            // ->groupBy('student.id')
        ;

        // return $model->tableName();
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->andWhere([
                'faculty_id' => $t['UserAccess']->table_id
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
        $data =  $this->getData($query);
        $data =  $data->asArray();

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
            ['Britney',  "Spears", "02-12-1981"],
            ['Michael',  "Jackson", "29-08-1958"],
            ['Christina',  "Aguilera", "18-12-1980"],
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

        if (!file_exists(STORAGE_PATH  . 'student_export')) {
            mkdir(STORAGE_PATH  . 'student_export', 0777, true);
        }

        $fileName = time() . '_std.xlsx';

        $miniUrl =  'student_export/' . $fileName;
        $url = STORAGE_PATH . $miniUrl;
        $writer->save($url, false);
        $excel_url =  "storage/" . $miniUrl;


        return $this->response(1, _e('Success'), $excel_url);
    }

    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $post['faculty_id'] = $t['UserAccess']->table_id;
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        if (isRole('tutor')) {
            $post['tutor_id'] = Current_user_id();
        }
        $post['role'] = 'student';
        $model = new User();
        $profile = new Profile();
        $student = new Student();

        $users = Student::find()->count();
        $std = Student::find()->orderBy(['id' => SORT_DESC])->one();
        $count = $users + 10001;
        if ($std) {
            $count = $std->id + 10001;
        }

        $post['username'] = 'tsul-std-' . $count;
        if (!(isset($post['email']))) {
            $post['email'] = 'tsul-std-' . $count . '@tsul.uz';
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

    public function actionUpdateMe($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $profile = ProfileSelf::findOne(['user_id' => $student->user_id]);

        $data = [];

        if (isset($post['region_id'])) $data['region_id'] = $post['region_id'];
        if (isset($post['area_id'])) $data['area_id'] = $post['area_id'];
        if (isset($post['address'])) $data['address'] = $post['address'];
        if (isset($post['res_person_phone'])) $data['res_person_phone'] = $post['res_person_phone'];
        if (isset($post['parent_phone'])) $data['parent_phone'] = $post['parent_phone'];
        if (isset($post['phone'])) $data['phone'] = $post['phone'];
        if (isset($post['phone_secondary'])) $data['phone_secondary'] = $post['phone_secondary'];
        if (isset($post['country_id'])) $data['country_id'] = $post['country_id'];
        if (isset($post['car_number'])) $data['car_number'] = $post['car_number'];
        if (isset($post['citizenship_id'])) $data['citizenship_id'] = $post['citizenship_id'];


        // $profile->load($data);
        $this->load($profile, $data);

        // dd(['data' => $data, 'phone' => $profile->phone]);
        $result = StudentUser::updateMe($profile, $data);
        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $profile, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
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
            if ($student->faculty_id != $t['UserAccess']->table_id) {
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


        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($student, $post);
        $result = StudentUser::updateItem($model, $profile, $student, $post);

        $data = [];
        $data['student'] = $student;
        $data['profile'] = $profile;
        $data['user'] = $model;

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $data, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
    public function actionImage($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($student->faculty_id != $t['UserAccess']->table_id) {
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


        // $this->load($model, $post);
        // $this->load($profile, $post);
        // $this->load($student, $post);
        $result = StudentUser::updateImage($model, $profile, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('Student image successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Student::findOne(['id' => $id]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
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
            if ($model->faculty_id != $t['UserAccess']->table_id) {
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

    public function actionStudyTypes($lang)
    {
        $model = new Student();
        return $model->typesArray();
    }
}
