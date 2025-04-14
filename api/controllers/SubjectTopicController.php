<?php

namespace api\controllers;


use common\models\model\SubjectTopic;
use Yii;
use base\ResponseStatus;


class SubjectTopicController extends ApiActiveController
{

    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public $table_name = 'subject_topic';
    public $controller_name = 'SubjectTopic';

    public function actionIndex($lang)
    {
        $model = new SubjectTopic();

        // return $this->teacher_access(1, ['lang_id']);

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole(('teacher') && (!isRole('mudir'))) && (isRole('teacher') && (!isRole('contenter')))) {
            $query->andWhere(['in', 'lang_id', $this->teacher_access(1, ['language_id'])]);
        }
        if (isRole('student')) {
            $query->andWhere(['lang_id' => $this->student(2) ?? $this->student(2)->edu_lang_id]);
        }

        // dd($query->createCommand()->getSql());

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
        $model = new SubjectTopic();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = SubjectTopic::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SubjectTopic::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = SubjectTopic::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = SubjectTopic::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SubjectTopic::find()
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
