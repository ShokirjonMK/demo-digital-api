<?php

namespace api\controllers;

use api\resources\User;
use common\models\model\EduPlan;
use common\models\model\Group;
use common\models\model\Profile;
use common\models\model\TeacherAccess;
use common\models\model\Timetable;
use common\models\model\TimeTable1;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\model\TimeTableGroup;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use yii\db\Expression;
use yii\db\Query;

class TimetableAttendController extends ApiActiveController
{
    public $modelClass = 'common\models\model\TimetableAttend';

    public $table_name = 'timetable_attend';

    public $controller_name = 'TimetableAttend';

    public function actions()
    {
        return [];
    }

    public function actionIndex()
    {
        $model = new TimetableAttend();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query->andFilterWhere(['student_id' => current_student()->id]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionGet()
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');

        $timeTableQuery = TimetableAttend::find()
            ->select('student_id')
            ->where([
                'is_deleted' => 0,
                'edu_year_id' => Yii::$app->request->get('edu_year_id')
            ]);

        if (isRole('teacher')) {
            $timeTableQuery = $timeTableQuery->andWhere(['created_by' => current_user_id()]);
        }

        $query->andWhere(['in', 'student.id', $timeTableQuery]);


        //  Filter from Profile
        $profile = new Profile();
        $queryfilter = Yii::$app->request->get('filter-like');
        if (isset($queryfilter)) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if (isset($queryfilter)) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $profile->attributes())) {
                        $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionAttendStudent()
    {
        $model = new TimetableAttend();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('teacher')) {
            $query->andWhere(['created_by' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionUpdate($lang, $id)
    {
        $model = TimetableAttend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = TimetableAttend::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();
        $result = TimetableAttend::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully saved.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStudentReason($lang)
    {
        $post = Yii::$app->request->post();
        $result = TimetableAttend::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully saved.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}
