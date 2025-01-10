<?php

namespace api\controllers;

use api\resources\SemestrUpdate;
use common\models\model\FinalExam;
use common\models\model\Group;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class StudentGroupController extends ApiActiveController
{
    public $modelClass = 'common\models\model\StudentGroup';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_group';
    public $controller_name = 'StudentGroup';

    public function actionIndex($lang)
    {
        $model = new StudentGroup();

        $query = $model->find()
            ->where(['is_deleted' => 0]);

        if (isRole('student')) {
            $query->andWhere(['student_id' => current_student()->id]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $post = Yii::$app->request->post();
        $result = StudentGroup::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentGroup::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = StudentGroup::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $studentGroup = StudentGroup::findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$studentGroup) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = SemestrUpdate::deleteItem($studentGroup);

        if ($result['is_ok']) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}
