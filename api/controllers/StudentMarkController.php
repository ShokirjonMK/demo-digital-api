<?php

namespace api\controllers;

use common\models\model\FinalExam;
use common\models\model\Group;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentMark;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class StudentMarkController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_mark';
    public $controller_name = 'StudentMark';

    public function actionIndex($lang)
    {
        $model = new StudentMark();

        $query = $model->find()
            ->where(['is_deleted' => 0]);

        if (isRole('student')) {
            $student = Student::findOne(['user_id' => current_user_id()]);
            if ($student) {
                $query->andWhere(['student_id' => $student->id]);
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

    public function actionGet($lang)
    {
        $model = new StudentMark();
        $student = new Student();

        $groupId = Yii::$app->request->get('group_id');

        $eduYearId = Yii::$app->request->get('edu_year_id');
        if ($eduYearId == null) {
            $eduYearId = activeYearId();
        }

        $eduSemestrId = Yii::$app->request->get('edu_semestr_id');

        if (isRole('teacher')) {
            $group = Group::findOne($groupId);
            $eduSemestrId = $group->activeEduSemestr->id;
        }

        $studentIdsQuery = StudentGroup::find()
            ->select('student_id')
            ->where([
                'edu_year_id' => $eduYearId,
                'edu_semestr_id' => $eduSemestrId,
                'status' => 1,
                'is_deleted' => 0
            ]);

        if ($groupId) {
            $studentIdsQuery->andWhere(['group_id' => $groupId]);
        }

        $subQuery = $student->find()
            ->select('id')
            ->where(['is_deleted' => 0])
            ->andWhere(['in' , 'id', $studentIdsQuery]);

        if (isRole('tutor')) {
            $subQuery->andWhere([
                'tutor_id' => current_user_id(),
            ]);
        }

        $query = $model->find()
            ->where(['is_deleted' => 0])
            ->andWhere(['in' , 'student_id', $subQuery]);

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
        $post = Yii::$app->request->post();

        $result = StudentMark::createItem($post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionStudentMarkUpdate($lang)
    {
        $post = Yii::$app->request->post();
        $result = StudentMark::updateItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionFinalExam($lang, $id)
    {
        $model = FinalExam::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status != 3) {
            return $this->response(0, _e('Evaluation not allowed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        if (isRole('teacher') || isRole('tutor')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }
        $post = Yii::$app->request->post();
        $result = StudentMark::finalExam($post, $model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExam()
    {
        $post = Yii::$app->request->post();
        $result = StudentMark::examItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentMark::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentMark::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
