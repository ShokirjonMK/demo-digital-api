<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\FinalExam;
use common\models\model\FinalExamGroup;
use common\models\Subject;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class FinalExamController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'final_exam';
    public $controller_name = 'FinalExam';

    public function actionIndex($lang)
    {
        $model = new FinalExam();

        $query = $model->find()->where(['is_deleted' => 0]);

        if (isRole('dean')) {
            $faculty = get_dean();
            if ($faculty != null) {
                $query->andWhere(['faculty_id' => $faculty->id]);
            } else {
                $query->andWhere(['status' => -1]);
            }
        }

        if (isRole('test_department')) {
            $query->andWhere(['faculty_id' => 6]);
        }

        if (isRole('teacher') || isRole('tutor')) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        if (isRole('mudir')) {
            $kafedra = get_mudir();
            if ($kafedra != null) {
                $query->andWhere([
                    'in',
                    'subject_id',
                    Subject::find()->select('id')->where(['kafedra_id' => $kafedra->id])
                ]);
            } else {
                $query->andWhere(['status' => -1]);
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
        $model = new FinalExam();
        $post = Yii::$app->request->post();

        if (isset($post['start_time'])) {
            $post['start_time'] = strtotime($post['start_time']);
        }

        if (isset($post['finish_time'])) {
            $post['finish_time'] = strtotime($post['finish_time']);
        }

        $this->load($model, $post);

        $result = FinalExam::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = FinalExam::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status > 1) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        $post = Yii::$app->request->post();

        if (isset($post['start_time'])) {
            $post['start_time'] = strtotime($post['start_time']);
        }

        if (isset($post['finish_time'])) {
            $post['finish_time'] = strtotime($post['finish_time']);
        }

        $this->load($model, $post);
        $result = FinalExam::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        if (isRole('teacher') || isRole('tutor')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionConfirm($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (!($model->status == 1 || $model->status == 2)) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        if (isRole('dean')) {
            $dean = get_dean();
            if ($dean != null) {
                if ($model->faculty_id != $dean->id) {
                    return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
                }
            } else {
                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }

        $post = Yii::$app->request->post();
        $result = FinalExam::confirm($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionEduTypeUpdate($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        if ($model->status > 3) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();
        $result = FinalExam::eduTypeUpdate($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConfirmTwo($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        if (!($model->status == 2 || $model->status == 3)) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $result = FinalExam::confirmTwo($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionInCharge($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        if (isRole('teacher') || isRole('tutor') || isRole('mentor')) {
            if ($model->user_id != current_user_id() || $model->exams_type_id != 3) {
                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
            } else {
                if (!($model->status == FinalExam::STATUS_DEFAULT || $model->status == FinalExam::STATUS_CHARGE)) {
                    return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }
        }

        $post = Yii::$app->request->post();
        $result = FinalExam::inCharge($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConfirmMudir($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

//        if (isRole('mudir')) {
//            $kafedra = get_mudir();
//            if ($kafedra) {
//                if ($model->subject->kafedra_id != $kafedra->id) {
//                    return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
//                } else {
//                    if (!($model->status == FinalExam::STATUS_CHARGE || $model->status == FinalExam::STATUS_MUDIR)) {
//                        return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
//                    }
//                }
//            } else {
//                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
//            }
//        }

        $post = Yii::$app->request->post();
        $result = FinalExam::confirmMudir($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConfirmDean($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        if (isRole('dean')) {
            $dean = get_dean();
            if ($dean != null) {
                if ($model->faculty_id != $dean->id) {
                    return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
                } else {
                    if (!($model->status == FinalExam::STATUS_MUDIR || $model->status == FinalExam::STATUS_DEAN)) {
                        return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                    }
                }
            } else {
                return $this->response(0, _e('This information will not be shown to you.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }

        $post = Yii::$app->request->post();
        $result = FinalExam::confirmDean($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionLastConfirm($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (!($model->status == 6 || $model->status == 7)) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        if (isRole('test_department')) {
            if ($model->faculty_id != 6) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        $post = Yii::$app->request->post();
        $result = FinalExam::confirmLast($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAllConfirm($lang)
    {
        $post = Yii::$app->request->post();

        $model = FinalExam::find()
            ->where(['status' => 6, 'is_deleted' => 0])
            ->andWhere(['vedomst' => $post['vedomst']])
            ->all();
        if (count($model) == 0) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::OK);
        }

        $post = Yii::$app->request->post();
        $result = FinalExam::allConfirm($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = FinalExam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            FinalExamGroup::updateAll(['is_deleted' => 1] , ['final_exam_id' => $model->id]);
            $model->is_deleted = 1;
            $model->update(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}