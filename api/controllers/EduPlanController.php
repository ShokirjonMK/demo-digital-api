<?php

namespace api\controllers;

use common\models\model\EduPlan;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Semestr;
use common\models\model\TimeTable1;
use common\models\model\TimetableDate;
use common\models\Student;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Faculty;

class EduPlanController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduPlan';

    public function actions()
    {
        return [];
    }

    public $table_name = 'edu_plan';
    public $controller_name = 'EduPlan';

    public function actionIndex($lang)
    {
        $model = new EduPlan();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            //            ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (isRole('teacher')) {
            $query->andWhere(['in' , $this->table_name .'.id' , TimetableDate::find()
                ->select('edu_plan_id')
                ->where([
                    'user_id' => current_user_id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ])]);
        } elseif (!isRole('rector') && !isRole('prorector')) {

            if (isRole('tutor')) {
                $query->andWhere(['in' , $this->table_name .'.id' , Student::find()
                    ->select('edu_plan_id')
                    ->where([
                        'tutor_id' => current_user_id(),
                        'status' => 10,
                        'is_deleted' => 0
                    ])
                ]);
            } else {
                $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
                if ($t['status'] == 1) {
                    $query->andFilterWhere([
                        $this->table_name.'.faculty_id' => $t['UserAccess']
                    ]);
                } elseif ($t['status'] == 2) {
                    $query->andFilterWhere([
                        $this->table_name.'.faculty_id' => -1
                    ]);
                }
            }
        }


        $courseId = Yii::$app->request->get('course_id');
        if (isset($courseId)) {
            $query = $query->andFilterWhere([
                'in' , $this->table_name . '.id' , EduSemestr::find()->select('edu_plan_id')->where([
                    'course_id' => $courseId,
                    'is_checked' => 1,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
            ]);
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
        $model = new EduPlan();
        $post = Yii::$app->request->post();

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $post['faculty_id'] = $t['UserAccess'];
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $this->load($model, $post);
        $result = EduPlan::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduPlan successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        $model = EduPlan::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // is Self 
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess']) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = EduPlan::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('EduPlan successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduPlan::find()
            ->andWhere(['id' => $id])
            ->andWhere(['is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // is Self
        if (isRole('teacher')) {
            $query = TimeTable1::find()
                ->andWhere(['in' , 'edu_plan_id' ,
                    TimeTable1::find()
                        ->select('edu_plan_id')
                        ->where(['edu_plan_id' => $model->id , 'user_id' => current_user_id(), 'status' => 1 , 'is_deleted' => 0])])->all();
            if (count($query) <= 0) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                if ($model->faculty_id != $t['UserAccess']) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                }
            } elseif ($t['status'] == 2) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = EduPlan::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // is Self 
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->faculty_id != $t['UserAccess']) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        // remove model
        $result = EduPlan::findOne($id);

        if ($result) {
            $result->is_deleted = 1;
            $result->save(false);

            return $this->response(1, _e('EduPlan succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
