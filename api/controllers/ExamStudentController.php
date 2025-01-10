<?php

namespace api\controllers;

use common\models\model\ExamStudent;
use Yii;
use base\ResponseStatus;
use common\models\model\ExamControl;
use common\models\model\Faculty;
use common\models\model\Student;

class ExamStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\Exam';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student';
    public $controller_name = 'ExamStudent';

    public function actionIndex($lang)
    {
        $model = new ExamStudent();
        $query = $model->find();
        $query = $query->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (isRole('student')) {
            $student = Student::findOne([
                'user_id' => current_user_id(),
                'is_deleted' => 0
            ]);
            if (isset($student)) {
                $query->andWhere(['group_id' => $student->group_id]);
            } else {
                $query->andWhere(['is_deleted' => -1]);
            }
        }

        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);
        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionRating($lang , $id) {
        $model = ExamStudent::findOne([
            'id' => $id,
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamStudent::studentRating($model , $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionFinish($lang , $id) {
        $model = ExamStudent::findOne([
            'id' => $id,
            'student_user_id' => current_user_id(),
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamStudent::finish($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreate($lang)
    {
        $model = new ExamStudent();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = ExamStudent::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamStudent::findOne($id);
        $post = Yii::$app->request->post();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $type = $model->type;
        $this->load($model, $post);

        if (isset($post['start'])) {
            if ($model->start_time < time()) {
                return $this->response(0, _e('Exam Control has started. You cannot change the data.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
            $model['start_time'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish_time'] = strtotime($post['finish']);
        }

        $result = ExamStudent::updateItem($model, $type);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        if (isRole('student')) {
            $query = ExamStudent::find()->where([
                    'id' => $id,
                    'student_user_id' => current_user_id(),
                    'is_deleted' => 0
                ])->one();
        } else {
            $query = ExamStudent::find()->where([
                    'id' => $id,
                    'is_deleted' => 0
                ])->one();
        }
        if ($query == null) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $query, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamStudent::find()->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->save(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

}
