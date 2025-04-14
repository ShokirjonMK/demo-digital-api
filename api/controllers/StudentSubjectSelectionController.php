<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\StudentSubjectSelection;
use common\models\User;
use Yii;

class StudentSubjectSelectionController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentSubjectSelection';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_subject_selection';
    public $controller_name = 'StudentSubjectSelection';


    public function actionIndex($lang)
    {
        $model = new StudentSubjectSelection();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0]);

        if (isRole('student')) {
            $query->andWhere([$this->table_name . '.user_id' => current_user_id()]);
        }

        $query
            ->with(['profile'])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student_subject_selection.user_id')
            ->join('INNER JOIN', 'users', 'users.id = student_subject_selection.user_id')
            // ->groupBy('student.id')
        ;

        //  Filter from Profile 
        $profile = new Profile();
        $user = new User();
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $user->attributes())) {
                    $query = $query->andFilterWhere(['users.' . $attribute => $id]);
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
                if (in_array($attributeq, $user->attributes())) {
                    $query = $query->andFilterWhere(['like', 'users.' . $attributeq, '%' . $word . '%', false]);
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
        $model = new StudentSubjectSelection();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = StudentSubjectSelection::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentSubjectSelection::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }


        if (isRole('student')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours!'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $result = StudentSubjectSelection::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = StudentSubjectSelection::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('student')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours!'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    public function actionDelete($lang, $id)
    {
        $model = StudentSubjectSelection::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            if (isRole('student')) {
                if ($model->user_id != current_user_id()) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours!'), ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }
            $model->delete();
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}
