<?php

namespace api\controllers;

use common\models\model\TimeOption;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Faculty;

class TimeOptionController extends ApiActiveController
{
    public $modelClass = 'api\resources\TimeOption';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new TimeOption();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            // ->andWhere(['archived' => 0])
        ;

        if (isRole('student')) {
            $student = $this->student(2);
            if ($student) {
                $query->andWhere(['language_id' => $student->edu_lang_id]);
                $query->andWhere(['edu_plan_id' => $student->edu_plan_id]);
            }
            // $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()->where(['edu_plan_id' => $student->edu_plan_id, 'status' => 1])->select('id')]);
        } else {
            /*  is Self  */
            // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            // if ($t['status'] == 1) {
            //     $query->andFilterWhere([
            //         'faculty_id' => $t['UserAccess']->table_id
            //     ]);
            // } elseif ($t['status'] == 2) {
            //     $query->andFilterWhere([
            //         'faculty_id' => -1
            //     ]);
            // }
            // /*  is Self  */
        }

        // faculty_id
        if (isRole("dean")) {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    'faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
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
        $model = new TimeOption();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TimeOption::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeOption successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = TimeOption::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = TimeOption::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeOption successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TimeOption::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TimeOption::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = TimeOption::findOne($id);

        if ($result) {
            $result->delete();

            return $this->response(1, _e('TimeOption succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
