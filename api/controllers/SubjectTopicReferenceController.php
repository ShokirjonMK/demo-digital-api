<?php

namespace api\controllers;

use common\models\model\SubjectTopicReference;
use Yii;
use base\ResponseStatus;


class SubjectTopicReferenceController extends ApiActiveController
{
    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public $table_name = 'subject_topic_reference';
    public $controller_name = 'SubjectTopicReference';

    public function actionIndex($lang)
    {
        $model = new SubjectTopicReference();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0]);


        // if (isRole('teacher') && (!isRole('mudir') && !isRole('contenter'))) {
        //     // if (isRole('teacher') && !isRole('mudir')) {
        //     $query->andWhere([$this->table_name . '.user_id' => current_user_id()]);
        // }

        if (Yii::$app->request->get('user_id') != null) {
            $query->andWhere([$this->table_name . '.user_id' => Yii::$app->request->get('user_id')]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new SubjectTopicReference();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = SubjectTopicReference::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SubjectTopicReference::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = SubjectTopicReference::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = SubjectTopicReference::find()
            ->andWhere(['id' => $id])
            ->andWhere(['is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SubjectTopicReference::find()
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
