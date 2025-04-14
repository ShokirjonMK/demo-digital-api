<?php

namespace api\controllers;

use Yii;

use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Profile;
use common\models\model\Semestr;
use common\models\model\Student;
use common\models\model\StudentTimeOption;

class StudentTimeOptionController extends ApiActiveController
{

    public $modelClass = 'api\resources\StudentTimeOption';

    public function actions()
    {
        return [];
    }
    public $table_name = 'student_time_option';
    public $controller_name = 'StudentTimeOption';

    public function actionIndex($lang)
    {
        $model = new StudentTimeOption();
        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $this->table_name . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');

        $student = Student::findOne(['user_id' => Current_user_id()]);

        $semester = Semestr::findOne(Yii::$app->request->get('semester_id'));

        if (isRole('student') && $student) {
            if ($semester) {
                $eduSemestr = EduSemestr::findOne(['edu_plan_id' => $student->edu_plan_id, 'semestr_id' => $semester->id]);
            } else {
                $eduSemestr = EduSemestr::findOne(['edu_plan_id' => $student->edu_plan_id, 'status' => 1]);
            }

            // return $eduSemestr;
            if ($eduSemestr) {
                $query->andWhere([$this->table_name . '.edu_semester_id' => $eduSemestr->id]);
            }
            $query->andWhere([$this->table_name . '.student_id' => $student->id]);
        } else {
            if ($semester) {
                $eduSemestr = EduSemestr::findOne(['semestr_id' => $semester->id]);
                if ($eduSemestr) {
                    $query->andWhere([$this->table_name . '.edu_semester_id' => $eduSemestr->id]);
                }
            }
        }

        //  Filter from Profile 
        $profile = new Profile();
        $student = new Student();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                }
                if (in_array($attribute, $student->attributes())) {
                    $query = $query->andFilterWhere(['student.' . $attribute => $id]);
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
                if (in_array($attributeq, $student->attributes())) {
                    $query = $query->andFilterWhere(['like', 'student.' . $attributeq, '%' . $word . '%', false]);
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
        $model = new StudentTimeOption();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentTimeOption::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        $model = StudentTimeOption::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $model_new = new StudentTimeOption();
        $post = Yii::$app->request->post();
        $this->load($model_new, $post);

        $result = StudentTimeOption::updateItem($model_new, $post, $model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {

        // $ids = [46437, 43386, 46435, 44828, 42428, 45395, 45583, 43568, 43569, 44503, 45083, 45283, 45749, 46105, 45431, 46112, 46113, 46099, 45138, 46283, 45482, 46557, 46645, 46534];
        // $errors = [];
        // if (is_array($ids) && count($ids) > 0) {


        //     // Debugging output for verification
        //     // dd($ids);  // Optional: remove or comment out after debugging

        //     foreach ($ids as $id) {
        //         $model = StudentTimeOption::findOne(['id' => $id]);
        //         if (!$model) {
        //             continue;
        //         }
        //         $result = StudentTimeOption::deleteItem($model);
        //         if (is_array($result)) {
        //             $errors[] = $result;
        //         }
        //     }

        //     if (count($errors) > 0) {
        //         return $this->response(0, _e('There was an error processing the request.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        //     } else {
        //         return $this->response(1, _e('All data successfully deleted.'), null, null, ResponseStatus::OK);
        //     }
        // } else {
        //     return $this->response(0, _e('Invalid or empty "ids" parameter provided.'), null, null, ResponseStatus::BAD_REQUEST);
        // }


        $model = StudentTimeOption::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete012($lang, $id)
    {
        $model = StudentTimeOption::findOne(['id' => $id]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->timeOption->archived != 0) {
            return $this->response(0, _e('Old Option can not be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        // remove model
        // $result = ;

        if ($model->delete()) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }


    public function actionDelete1($lang, $id)
    {
        $model = StudentTimeOption::findOne(['id' => $id]);
        if (!$model) {

            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->timeOption->archived != 0) {
            return $this->response(0, _e('Old Option can not be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // remove model
        $result = StudentTimeOption::deleteItemWithRels($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if ($result) {
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentTimeOption::findOne(['id' => $id]);
        if (!$model) {

            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->timeOption->archived != 0) {
            return $this->response(0, _e('Old Option can not be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);

            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        // remove model
        $result = StudentTimeOption::deleteItem($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if ($result) {
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::NO_CONTENT);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
