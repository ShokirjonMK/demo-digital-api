<?php

namespace api\controllers;

use common\models\model\Attend;
use Yii;
use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\Kafedra;
use common\models\model\Subject;
use common\models\model\TimeTable;

class AttendController extends ApiActiveController
{
    public $modelClass = 'api\resources\Attend';

    public function actions()
    {
        return [];
    }

    public $table_name = 'attend';
    public $controller_name = 'Attend';

    public function actionIndex($lang)
    {
        $model = new Attend();

        $query = $model->find()
            // ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])

            // ->join("INNER JOIN", "translate tr", "tr.model_id = $model->tableName().id and tr.table_name = '$model->tableName()'" )
        ;

        // if (isRole('student')) {
        //     $query->andWhere([$model->tableName() . '.student_id' => $this->student()]);
        // }

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
        $model = new Attend();
        $post = Yii::$app->request->post();
        if (!isset($post['date'])) {
            $post['date'] = date('Y-m-d');
        }
        /* else{
            $post['date'] = date('Y-m-d', strtotime($post['date']));
        } */

        $this->load($model, $post);

        $result = Attend::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Attend::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $student_ids = $model->student_ids;
        unset($post['date']);

        unset($post['subject_id']);
        unset($post['subject_category_id']);
        unset($post['time_option_id']);
        unset($post['edu_year_id']);
        unset($post['edu_semestr_id']);
        unset($post['faculty_id']);
        unset($post['edu_plan_id']);
        unset($post['type']);
        unset($post['semestr_id']);

        $this->load($model, $post);
        $result = Attend::updateItem($model, $post, $student_ids);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionNot($date)
    {
        $model = new TimeTable();
        $date = date("Y-m-d", strtotime($date));
        $N = date('N', strtotime($date));
        /*
                    		
            SELECT
            	* 
            	* 
            FROM
            	`time_table` 
            WHERE
            	( `time_table`.`is_deleted` = 0 ) 
            	AND ( `time_table`.`archived` = 0 ) 
            	AND ( `id` NOT IN ( SELECT `time_table_id` FROM `attend` WHERE `date` = '2023-04-19' ) ) 
            	AND ( `time_table`.`week_id` = '3' )
        */

        $query = $model->find()
            ->andWhere([$model->tableSchema->name . '.is_deleted' => 0])
            ->andWhere([$model->tableSchema->name . '.archived' => 0]);


        $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
        if ($k['status'] == 1) {

            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => $k['UserAccess']->table_id
                ])->select('id')
            ]);
        }


        if (isRole('teacher') && !isRole('mudir')) {
            $query->andFilterWhere([
                'teacher_user_id' => current_user_id()
            ]);
        }

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => $kafedraId
                ])->select('id')
            ]);
        }

        $query = $query->andWhere([
            'not in', 'id', Attend::find()
                ->select('time_table_id')
                ->andWhere(['date' => $date])
        ])
            ->andWhere([$model->tableSchema->name . '.week_id' => $N]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        // rawsql($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionView($lang, $id)
    {
        $model = Attend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Attend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            // $model->delete();
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
