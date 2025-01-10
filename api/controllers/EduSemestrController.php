<?php

namespace api\controllers;

use common\models\model\EduSemestr;
use Yii;
use base\ResponseStatus;
use common\models\model\EduPlan;
use common\models\model\Faculty;
use common\models\model\Student;

class EduSemestrController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestr';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduSemestr();



        if (isRole('student')) {

            $student = Student::findOne(['user_id' => current_user_id()]);
            if (isset($student)) {
                if ($student->group == null) {
                    return $this->response(0, _e('The student has not joined the group.'), null, null, ResponseStatus::FORBIDDEN);
                }
            }

            $query = $model->find()
                ->where([
                    'is_deleted' => 0,
                    'edu_plan_id' => $student->group->edu_plan_id
                ])
                ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);
        } else {
            $query = $model->find()
                ->andWhere(['edu_semestr.is_deleted' => 0])
                ->andFilterWhere(['like', 'edu_semestr.name', Yii::$app->request->get('query')]);

            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $eduPlan = EduPlan::find()->where(['faculty_id'=> $t['UserAccess']])->select('id');
                $query->andWhere(['in', 'edu_plan_id', $eduPlan]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'edu_plan_id' => -1
                ]);
            }
            /*  is Self  */

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
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);

        $model = new EduSemestr();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestr::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduSemestr successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = EduSemestr::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->edu_year_id == 7 || $model->edu_year_id == 8) {
//            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduSemestr::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduSemestr successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestr::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
        $model = EduSemestr::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        // remove model
        $result = EduSemestr::findOne($id);

        if ($result) {
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Edu Semestr succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
