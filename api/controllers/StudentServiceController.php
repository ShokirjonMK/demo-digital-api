<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentService;
use yii\caching\DbDependency;

class StudentServiceController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentService';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_service';
    public $controller_name = 'StudentService';

    public function actionIndex($lang)
    {
        $model = new StudentService();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');


        //  Filter from student 
        $student = new Student();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $student->attributes())) {
                    $query = $query->andFilterWhere(['student.' . $attribute => $id]);
                }
            }
        }

        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
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
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student_service']));

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionMy($lang)
    {
        $model = new StudentService();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');


        $query->andWhere([$model->tableName() . '.created_by' => current_user_id()]);

        //  Filter from student 
        $student = new Student();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $student->attributes())) {
                    $query = $query->andFilterWhere(['student.' . $attribute => $id]);
                }
            }
        }

        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
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
            }
        }
        // ***

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM student_service']));

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionView($lang, $id)
    {
        $model = StudentService::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success'), $model);
    }

    public function actionCreate($lang)
    {
        $model = new StudentService();

        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentService::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentService::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentService::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentService::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model->is_deleted = 1;
        $model->save(false);

        return $this->response(1, _e('Deleted successfully'));
    }

    public function actionRespond($lang, $id)
    {
        $model = StudentService::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        // $model->answer_text = $post['answer_text'] ?? $model->answer_text;
        // $model->user_id = current_user_id();

        $result = StudentService::respondItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
