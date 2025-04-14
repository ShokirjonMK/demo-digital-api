<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Exam;
use common\models\model\ExamTeacherCheck;
use common\models\model\Faculty;
use common\models\model\TeacherAccess;

class ExamTeacherCheckController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamTeacherCheck';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_teacher_check';
    public $controller_name = 'Exam Teacher Check';

    public function actionIndex($lang)
    {
        $model = new ExamTeacherCheck();

        $user_id = Current_user_id() ?? null;

        $roles = (object) \Yii::$app->authManager->getRolesByUser($user_id);
        if (property_exists($roles, 'teacher')) {
            $query = $model->find()->andWhere(['in', 'teacher_access_id', $this->teacher_access()]);
        } else {
            $query = $model->find()
                ->andWhere(['is_deleted' => 0]);
        }

        /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     $query = $query->andWhere([
        //         'faculty_id' => $t['UserAccess']->table_id
        //     ]);
        // } elseif ($t['status'] == 2) {
        //     $query->andFilterWhere([
        //         'faculty_id' => -1
        //     ]);
        // }
        /*  is Self  */

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionRandomStudents($lang)
    {
        $model = new ExamTeacherCheck();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = ExamTeacherCheck::randomStudent($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreate($lang)
    {
        $model = new ExamTeacherCheck();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = ExamTeacherCheck::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamTeacherCheck::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamTeacherCheck::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamTeacherCheck::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamTeacherCheck::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
