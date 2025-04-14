<?php

namespace api\controllers;

use common\models\model\TeacherAccess;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Profile;
use common\models\model\Semestr;
use common\models\model\TimeTable;
use common\models\User;

class TeacherAccessController extends ApiActiveController
{
    public $modelClass = 'api\resources\TeacherAccess';

    public function actions()
    {
        return [];
    }


    public $table_name = 'teacher_access';
    public $controller_name = 'TeacherAccess';

    public function actionContent($lang)
    {
        $model = new TeacherAccess();

        $query = $model->find()
            ->with(['teacher'])
            ->andWhere(['is_deleted' => 0]);


        if (isRole(('teacher') && (!isRole('mudir')))) {
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

    public function actionFree($lang)
    {
        $get = Yii::$app->request->get();
        $errors = [];
        if (empty(Yii::$app->request->get('para_id'))) {
            $errors[] = ['para_id' => _e('Para Id is required')];
        }
        if (empty(Yii::$app->request->get('edu_year_id'))) {
            $errors[] = ['edu_year_id' => _e('Edu Year Id is required')];
        }
        if (empty(Yii::$app->request->get('semester_id'))) {
            $errors[] = ['semester_id' => _e('Semester Id is required')];
        }
        if (empty(Yii::$app->request->get('week_id'))) {
            $errors[] = ['week_id' => _e('Week Id is required')];
        }

        if (count($errors) > 0) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $semester = Semestr::findOne(Yii::$app->request->get('semester_id'));

        if (!isset($semester)) {
            return $this->response(0, _e('Semester not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $type = $semester->type;

        $semester_ids = Semestr::find()->select('id')->where(['type' => $type]);

        $teacheIds =  TimeTable::find()
            ->select('teacher_user_id')
            ->where([
                'para_id' => Yii::$app->request->get('para_id'),
                'edu_year_id' => Yii::$app->request->get('edu_year_id'),
                'week_id' => Yii::$app->request->get('week_id'),
                'is_deleted' => 0,
                'status' => 1,
                'archived' => 0

            ])->andWhere(['in', 'semester_id', $semester_ids]);


        $model = new TeacherAccess();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->join('INNER JOIN', 'users', 'users.id = ' . $this->table_name . '.user_id');

        $query->andWhere(['users.deleted' => 0]);
        $query->andWhere(['users.status' => 10]);

        // sirtqi uchun ochildi
        // // magistr lar uchun ochildi
        // if (isset($teacheIds)) {
        //     $query->andFilterWhere(['not in', $model->tableName() . '.user_id', $teacheIds]);
        // }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {
        $model = new TeacherAccess();

        $query = $model->find()

            // ->join('INNER JOIN', 'profile', 'profile.user_id = ' . $this->table_name . '.user_id')
            ->join('INNER JOIN', 'users', 'users.id = ' . $this->table_name . '.user_id');

        // $query->andWhere(['users.status' => User::STATUS_ACTIVE, 'deleted' => 0]);
        $isDeleted = Yii::$app->request->get('is_deleted');
        if (!isset($isDeleted)) {
            $query->where([$this->table_name . '.is_deleted' => 0]);
        }


        //  Filter from Profile 
        // $profile = new Profile();
        // // $filter = Yii::$app->request->get('filter');
        // // $filter = json_decode(str_replace("'", "", $filter));
        // if (isset($filter)) {
        //     foreach ($filter as $attribute => $id) {
        //         if (in_array($attribute, $profile->attributes())) {
        //             $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
        //         }
        //     }
        // }

        // $queryfilter = Yii::$app->request->get('filter-like');
        // $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        // if (isset($queryfilter)) {
        //     foreach ($queryfilter as $attributeq => $word) {
        //         if (in_array($attributeq, $profile->attributes())) {
        //             $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
        //         }
        //     }
        // }
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
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);

        $model = new TeacherAccess();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TeacherAccess::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TeacherAccess successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = TeacherAccess::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = TeacherAccess::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TeacherAccess successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TeacherAccess::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TeacherAccess::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model->is_deleted = 1;
        if ($model->update()) {
            return $this->response(1, _e('TeacherAccess succesfully removed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $model->errors, ResponseStatus::BAD_REQUEST);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
