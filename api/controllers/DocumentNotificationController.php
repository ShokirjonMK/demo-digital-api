<?php

namespace api\controllers;

use common\models\model\Building;
use common\models\model\Document;
use common\models\model\DocumentFiles;
use common\models\model\DocumentNotification;
use common\models\model\DocumentNotificationInfo;
use common\models\model\EduSemestr;
use common\models\model\Letter;
use common\models\model\LetterFiles;
use common\models\model\LetterView;
use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduYear;
use common\models\model\Para;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\model\Week;

class DocumentNotificationController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'document_notification';
    public $controller_name = 'DocumentNotification';

    public function actionIndex($lang)
    {
        $model = new DocumentNotification();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

        if (!isRole('admin')) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionSign() {
        $model = new DocumentNotification();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

        if (!(isRole('rector') || isRole('admin'))) {
            $query->andWhere(['signature_user_id' => current_user_id()]);
        }

        $query->andWhere(['>' , 'type' , DocumentNotification::HR_FALSE]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionConfirm() {
        $model = new DocumentNotification();

        $query = $model->find()->andWhere(['is_deleted' => 0]);

        $query->andWhere(['status' => DocumentNotification::STATUS_TRUE]);

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
        $model = new DocumentNotification();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = DocumentNotification::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = DocumentNotification::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->user_id != current_user_id() || !isRole('admin')) {
            return $this->response(0, _e('The information does not belong to you.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        if ($model->status == DocumentNotification::STATUS_TRUE) {
            return $this->response(0, _e('The data cannot be changed.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = DocumentNotification::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionHrUpdate($lang, $id) {
        $model = DocumentNotification::find()
            ->andWhere([
                'id' => $id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->type != DocumentNotification::TYPE_DEFAULT) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();

        $result = DocumentNotification::hrUpdateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCommandType($lang, $id) {
        $model = DocumentNotification::find()
            ->andWhere([
                'id' => $id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->is_ok != DocumentNotification::IS_OK_TRUE) {
            return $this->response(0, _e('You cannot change the information.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();

        $result = DocumentNotification::commandType($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionSignatureUpdate($lang, $id) {
        $model = DocumentNotification::find()
            ->andWhere([
                'id' => $id,
                'status' => 1,
                'type' => DocumentNotification::HR_TRUE,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->signature_user_id != current_user_id()) {
            return $this->response(0, _e('You cannot delete this information!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        $post = Yii::$app->request->post();
        $result = DocumentNotification::signatureUpdateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = DocumentNotification::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (!(isRole('hr') || isRole('rector') || $model->user_id == current_user_id() || isRole('admin'))) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        } elseif (isRole('hr') || isRole('rector')) {
            $info = DocumentNotificationInfo::findOne([
                'document_notification_id' => $model->id,
                'user_id' => current_user_id(),
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($info == null) {
                $info = new DocumentNotificationInfo();
                $info->document_notification_id = $model->id;
                $info->user_id = current_user_id();
                $info->view_time = time();
                $info->save(false);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = DocumentNotification::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status == DocumentNotification::STATUS_TRUE) {
            return $this->response(0, _e('A sent message cannot be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (!isRole('admin')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('You cannot delete this information!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
