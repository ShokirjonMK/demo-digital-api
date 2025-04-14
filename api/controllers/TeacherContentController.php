<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\Subject;
use common\models\model\TeacherContent;
use Yii;

class TeacherContentController extends ApiActiveController
{
    public $modelClass = 'api\resources\TeacherContent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'teacher_subject_content';
    public $controller_name = 'TeacherContent';


    public function actionIndex($lang)
    {
        $model = new TeacherContent();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin('subject', "subject.id = $this->table_name.subject_id")
            ->leftJoin("translate tr", "tr.model_id = subject.id and tr.table_name = 'subject'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);;

        if (isRole('contenter') && !isRole('edu_quality')) {
            $query->andWhere(['in', $this->table_name . '.user_id', current_user_id()]);
        }

        //  Filter from subject 
        $subject = new Subject();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $subject->attributes())) {
                    $query = $query->andFilterWhere(['subject.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $subject->attributes())) {
                    $query = $query->andFilterWhere(['like', 'subject.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***

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
        $model = new TeacherContent();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TeacherContent::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = TeacherContent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TeacherContent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TeacherContent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    public function actionDelete($lang, $id)
    {
        $model = TeacherContent::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->delete()) {
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
