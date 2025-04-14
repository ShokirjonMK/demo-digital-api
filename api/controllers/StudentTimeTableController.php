<?php

namespace api\controllers;

use Yii;

use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\Semestr;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\TimeTable;

class  StudentTimeTableController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentTimeTable';

    public function actions()
    {
        return [];
    }
    public $table_name = 'student_time_table';
    public $controller_name = 'StudentTimeTable';

    public function actionIndex($lang)
    {
        $model = new StudentTimeTable();
        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])
            // ->andWhere([$this->table_name . '.archived' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $this->table_name . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');

        $student = Student::findOne(['user_id' => current_user_id()]);

        $semester = Semestr::findOne(Yii::$app->request->get('semester_id'));
        // $edu_semester = Semestr::findOne(Yii::$app->request->get('edu_semester_id'));

        if ($student && isRole('student')) {
            if ($semester) {
                $query->andWhere([$this->table_name . '.semester_id' => $semester->id]);
                // $eduSemestr = EduSemestr::findOne(['edu_plan_id' => $student->edu_plan_id, 'semestr_id' => $semester->id]);
            } else {
                $eduSemestr = EduSemestr::findOne(['edu_plan_id' => $student->edu_plan_id, 'status' => 1]);
                if ($eduSemestr) {
                    $query->andWhere([$this->table_name . '.edu_semester_id' => $eduSemestr->id]);

                    // $query->andWhere(['in', $this->table_name . '.time_table_id', TimeTable::find()
                    //     ->select('id')
                    //     ->where([$this->table_name . '.edu_semester_id' => $eduSemestr->id])]);
                }
            }
            // return $eduSemestr;

            $query->andWhere([$this->table_name . '.student_id' => $student->id]);
        } else {
            if ($semester) {

                $query->andWhere([$this->table_name . '.semester_id' => $semester]);

                /*  $eduSemestr = EduSemestr::findOne(['semestr_id' => $semester->id]);
                if ($eduSemestr) {

                    $query->andWhere([
                        'in', $this->table_name . '.time_table_id', TimeTable::find()
                            ->select('id')
                            ->where([$this->table_name . '.edu_semester_id' => $eduSemestr->id])
                    ]);
                } */
            }
        }


        //  Filter from Profile 
        $profile = new Profile();
        $student = new Student();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $student->attributes())) {
                    $query = $query->andFilterWhere(['student.' . $attribute => $id]);
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
                if (in_array($attributeq, $student->attributes())) {
                    $query = $query->andFilterWhere(['like', 'student.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

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

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $errors = [];
        if (!StudentTimeTable::chekTime()) {
            $errors[] = _e('This is not your time to choose!');
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $model = new StudentTimeTable();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentTimeTable::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        $model = StudentTimeTable::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model_new = new StudentTimeTable();
        $post = Yii::$app->request->post();
        $this->load($model_new, $post);

        $result = StudentTimeTable::updateItem($model_new, $post, $model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentTimeTable::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentTimeTable::findOne(['id' => $id]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // if ($model->timeTable->archived != 0) {
        //     return $this->response(0, _e('Old Student Time  can not be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        // }

        if (isRole('admin') || isRole('edu_admin') || isRole('tutor')) {
            $result = StudentTimeTable::deleteItem($model);
            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        if ($model->subject_category_id != 1) {
            if ($model->created_by == current_user_id() || isRole('admin')) {
                $result = StudentTimeTable::deleteItem($model);
                if (!is_array($result)) {
                    return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, _e('You can delete only seminars!'), ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
