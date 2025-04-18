<?php

namespace api\controllers;

use common\models\model\TimeTable;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\EduYear;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\Subject;
use common\services\TimeTableService;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

class TimeTableController extends ApiActiveController
{
    public $modelClass = 'api\resources\TimeTable';

    public function actions()
    {
        return [];
    }



    public function actionImport($lang)
    {
        // $data = [];

        $post = Yii::$app->request->post();
        // if ($post['type'] == 1) {
        $result = TimeTable::import($post);
        // }
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully imported.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while importing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }


        // $transaction = Yii::$app->db->beginTransaction();
        // $errors = [];
        // // $file = UploadedFile::getInstancesByName('excel');
        // $excel = UploadedFile::getInstancesByName('excel');
        // if (!$excel) {
        //     $errors[] = _e('Excel file required');
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // }

        // if ($excel) {
        //     $excel = $excel[0];
        //     $excelUrl = TimeTable::uploadExcel($excel);
        //     if ($excelUrl) {

        //         // dd($excelUrl);
        //         return true;
        //     } else {
        //         $errors[] = _e('Excel file not uploaded');
        //     }
        // }

        // dd("asas");
        // try {
        //     $inputFileType = IOFactory::identify($excel[0]->tempName);
        //     $objReader = IOFactory::createReader($inputFileType);

        //     $objectPhpExcel = $objReader->load($excel[0]->tempName);;

        //     $sheetDatas = [];

        //     $sheetDatas = $objectPhpExcel->getActiveSheet()->toArray(null, true, true, true);
        //     dd($sheetDatas);
        //     // if ($this->setFirstRecordAsKeys) {
        //     //     $sheetDatas = $this->executeArrayLabel($sheetDatas);
        //     // }

        //     // if (!empty($this->getOnlyRecordByIndex)) {
        //     //     $sheetDatas = $this->executeGetOnlyRecords($sheetDatas, $this->getOnlyRecordByIndex);
        //     // }
        //     // if (!empty($this->leaveRecordByIndex)) {
        //     //     $sheetDatas = $this->executeLeaveRecords($sheetDatas, $this->leaveRecordByIndex);
        //     // }

        //     dd($sheetDatas);
        //     foreach ($sheetDatas as $dataOne) {

        //         $timeTableNew = new TimeTable();
        //         $timeTableNew->room_id = $dataOne[0];
        //         $timeTableNew->para_id = $dataOne[1];
        //         $timeTableNew->week_id = $dataOne[2];
        //         $timeTableNew->edu_year_id = $dataOne[3];
        //         $timeTableNew->edu_plan_id = $dataOne[4];
        //         $timeTableNew->edu_semester_id = $dataOne[5];
        //         $timeTableNew->subject_id = $dataOne[6];
        //         $timeTableNew->language_id = $dataOne[7];
        //         $timeTableNew->teacher_access_id = $dataOne[8];
        //         $timeTableNew->time_option_id = $dataOne[9];

        //         if (!$timeTableNew->save()) {
        //             $errors[] = $timeTableNew->errors;
        //         }
        //     }
        // } catch (Exception $e) {
        //     $transaction->rollBack();
        // }

        // dd($errors);
        // if (count($errors) > 0) {
        //     $transaction->rollBack();
        //     return simplify_errors($errors);
        // } else {
        //     $transaction->commit();
        //     return [true];
        // }


        // return $sheetDatas;
    }

    public function actionIndex($lang)
    {
        $model = new TimeTable();
        $query = $model->find()->andWhere(['is_deleted' => 0]);

        $student = TimeTableService::getCurrentStudent();
        $archived = Yii::$app->request->get('archived');
        $this_year = Yii::$app->request->get('this_year');

        if ($this_year) {
            $query->andWhere(['in', 'edu_year_id', TimeTableService::getActiveEduYearIds()]);
        } elseif ($archived) {
            $query->andWhere(['archived' => 1]);
        } else {
            $query->andWhere(['archived' => 0]);
        }

        if (isRole('student') && $student) {
            $query->andWhere(['in', 'edu_semester_id', TimeTableService::getSemestersByPlan($student->edu_plan_id)]);
            $query->andWhere(['language_id' => $student->edu_lang_id]);
        } elseif (isRole('teacher') && !isRole('mudir') && !isRole('dean')) {
            $query->andFilterWhere(['teacher_user_id' => current_user_id()]);
        }

        $facultyId = Yii::$app->request->get('faculty_id');
        if ($facultyId) {
            $kafedra_ids = TimeTableService::getKafedraIdsByFaculty($facultyId);
            $subject_ids = TimeTableService::getSubjectIdsByKafedraIds($kafedra_ids);
            $query->andFilterWhere(['in', 'subject_id', $subject_ids]);
        } 

        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }



    public function actionIndex1($lang)
    {
        $model = new TimeTable();


        // Yii::$app->redis->set('mykey', 'myvalue');
        // $value = Yii::$app->redis->get('mykey');
        // // dd($value);

        // Yii::$app->redis->hset('user:1', 'name', 'John');
        // Yii::$app->redis->hset('user:1', 'email', 'john@example.com');

        // Yii::$app->redis->hset('user:2', 'name', 'John2');
        // Yii::$app->redis->hset('user:2', 'email', 'john2@example.com');

        // $userData = Yii::$app->redis->hgetall('user:2');
        // dd($userData);


        $archived = Yii::$app->request->get('archived');
        $this_year = Yii::$app->request->get('this_year');
        $student = Student::findOne(['user_id' => current_user_id()]);

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        $subject_category_ids = Yii::$app->request->get('subject_category_ids', []);
        if (is_array($subject_category_ids) && count($subject_category_ids) > 0) {
            $query->andFilterWhere(['in', 'subject_category_id', $subject_category_ids]);
        }


        if ($this_year) {
            $query->andWhere(['in', 'edu_year_id', EduYear::find()->where(['status' => 1])->select('id')]);
        } elseif ($archived) {
            $query->andWhere(['archived' => 1]);
        } else {
            $query->andWhere(['archived' => 0]);
        }

        // Apply role-based filters
        if (isRole('student')) {
            if ($student) {
                $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()
                    ->where(['edu_plan_id' => $student->edu_plan_id])
                    ->select('id')]);
                $query->andWhere(['language_id' => $student->edu_lang_id]);
            }
        } else {
            // Check if user has Kafedra access
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {
                $query->andFilterWhere([
                    'in',
                    'subject_id',
                    Subject::find()
                        ->where(['kafedra_id' => $k['UserAccess']->table_id])
                        ->select('id')
                ]);
            }
        }

        // Apply teacher filter
        if (isRole('teacher') && !isRole('mudir') && !isRole('dean') && !isRole('edu_quality') && !isRole('time_table')) {
            $query->andFilterWhere(['teacher_user_id' => current_user_id()]);
        }

        // Apply mudirSelf filter
        $mudirSelf = Yii::$app->request->get('self');
        if (isset($mudirSelf) && $mudirSelf == 1) {
            $query->andFilterWhere(['teacher_user_id' => current_user_id()]);
        }

        // Apply kafedraId filter
        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in',
                'subject_id',
                Subject::find()
                    ->where(['kafedra_id' => $kafedraId])
                    ->select('id')
            ]);
        }

        // Apply facultyId filter
        $facultyId = Yii::$app->request->get('faculty_id');
        if (isset($facultyId)) {
            $query->andFilterWhere([
                'in',
                'subject_id',
                Subject::find()
                    ->where(['kafedra_id' => Kafedra::find()->where(['faculty_id' => $facultyId])->select('id')])
                    ->select('id')
            ]);
        }

        // Apply subject_category_ids filter
        $subjectCategoryIds = json_decode(str_replace("'", "", Yii::$app->request->get('subject_category_ids')));
        if ($subjectCategoryIds) {
            $query->andFilterWhere([
                'in',
                'subject_category_id',
                $subjectCategoryIds
            ]);
        }

        // faculty_id
        if (isRole("dean")) {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $model->tableName() . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $model->tableName() . '.faculty_id' => -1
                ]);
            }
        }


        // Apply additional filtering, sorting, and data retrieval logic
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);
        // dd($query->createCommand()->getRawSql());

        // Retrieve and return the data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionParentNull($lang)
    {
        $model = new TimeTable();
        $archived = Yii::$app->request->get('archived');
        $this_year = Yii::$app->request->get('this_year');

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['parent_id' => null])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        $subject_category_ids = Yii::$app->request->get('subject_category_ids', []);
        if (is_array($subject_category_ids) && count($subject_category_ids) > 0) {
            $query->andFilterWhere(['in', 'subject_category_id', $subject_category_ids]);
        }


        $student = Student::findOne(['user_id' => current_user_id()]);
        if ($this_year) {
            $query->andWhere(['in', 'edu_year_id', EduYear::find()->where(['status' => 1])->select('id')]);
        } elseif ($archived) {
            $query->andWhere(['archived' => 1]);
        } else {
            $query->andWhere(['archived' => 0]);
        }
        if ($student && isRole('student')) {

            // /** Kurs bo'yicha vaqt belgilash */
            // $errors = [];
            // if (!StudentTimeTable::chekTime()) {
            //     $errors[] = _e('This is not your time to choose!');
            //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            // }
            // /** Kurs bo'yicha vaqt belgilash */

            $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()->where(['edu_plan_id' => $student->edu_plan_id])->select('id')]);
            $query->andWhere(['language_id' => $student->edu_lang_id]);
        } else {

            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {

                $query->andFilterWhere([
                    'in',
                    'subject_id',
                    Subject::find()->where([
                        'kafedra_id' => $k['UserAccess']->table_id
                    ])->select('id')
                ]);
            }
        }

        // if (isRole('teacher') && !isRole('mudir')) {
        //     $query->andFilterWhere([
        //         'teacher_user_id' => current_user_id()
        //     ]);
        // }

        // Apply teacher filter
        if (isRole('teacher') && !isRole('mudir') && !isRole('dean')) {
            $query->andFilterWhere(['teacher_user_id' => current_user_id()]);
        }

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in',
                'subject_id',
                Subject::find()->where([
                    'kafedra_id' => $kafedraId
                ])->select('id')
            ]);
        }

        // Apply subject_category_ids filter
        $subjectCategoryIds = json_decode(str_replace("'", "", Yii::$app->request->get('subject_category_ids')));
        if ($subjectCategoryIds) {
            $query->andFilterWhere([
                'in',
                'subject_category_id',
                $subjectCategoryIds
            ]);
        }

        // faculty_id
        if (isRole("dean")) {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $model->tableName() . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $model->tableName() . '.faculty_id' => -1
                ]);
            }
        }


        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query, 5);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        /* $errors = [];
        if (StudentTimeTable::TIME_10 < time()) {
            $errors[] = _e('Students started choosing!');
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        } */
        $model = new TimeTable();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TimeTable::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAddTeacher($lang, $id)
    {
        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = TimeTable::addTeacher($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDeleteTeacher($lang, $id)
    {
        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = TimeTable::deleteTeacher($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdateTeacher($lang, $id)
    {
        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = TimeTable::updateTeacher($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionUpdate($lang, $id)
    {
        /* $errors = [];
        if (StudentTimeTable::TIME_10 < time()) {
            $errors[] = _e('Students started choosing!');
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        } */

        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = TimeTable::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TimeTable::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('teacher') && !isRole('mudir') && !isRole('dean') && $model->teacher_user_id !== current_user_id()) {
            return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = TimeTable::findOne($id);

        if ($result) {
            TimeTable::deleteAll(['parent_id' => $result->id]);
            $result->delete();

            return $this->response(1, _e('TimeTable and its children succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
