<?php

namespace api\controllers;

use common\models\model\EduPlan;
use common\models\model\Group;
use common\models\model\TeacherAccess;
use common\models\model\Timetable;
use common\models\model\TimeTable1;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\model\TimeTableGroup;
use common\models\model\TimetableReason;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use yii\db\Expression;

class TimetableReasonController extends ApiActiveController
{
    public $modelClass = 'common\models\model\TimetableReason';

    public $table_name = 'timetable_reason';

    public $controller_name = 'TimetableReason';

    public function actions()
    {
        return [];
    }


    public function actionIndex()
    {
        $model = new TimetableReason();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        if (isRole('tutor')) {
            $query->andWhere(['in', 'id',
                Student::find()->select('id')->andWhere(['status' => 10 , 'is_deleted' => 0 , 'tutor_id' => current_user_id()])
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
        // return strtotime('-1 month');
        $model = new TimetableReason();
        $post = Yii::$app->request->post();
        unset($post['is_confirmed']);
        if (isset($post['start'])) {
            $post['start'] = date("Y-m-d H:i" , $post['start']);
        }
        if (isset($post['end'])) {
            $post['end'] = date("Y-m-d H:i" , $post['end']);
        }
        $this->load($model, $post);
        $result = TimetableReason::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        $model = TimetableReason::findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = TimetableReason::confirmItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Successfully confirmed.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = TimetableReason::findOne([
            'id' => $id, 'is_deleted' => 0
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->is_confirmed == 1) {
            return $this->response(0, _e('Confirmed information cannot be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $model->is_deleted = 1;
        $model->save(false);

        return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
    }
}
