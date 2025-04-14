<?php

namespace api\controllers;

use common\models\model\Room;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduYear;
use common\models\model\Para;
use common\models\model\Semestr;
use common\models\model\TimeTable;
use common\models\model\Week;

class RoomController extends ApiActiveController
{
    public $modelClass = 'api\resources\Room';

    public function actions()
    {
        return [];
    }

    public $table_name = 'room';
    public $controller_name = 'Room';


    public function actionFree($lang)
    {

        $post = Yii::$app->request->post();

        $errors = [];
        /**
         *  Ma'lumotlar to'g'ri jo'natilganligini tekshirish
         */
        if (isset($post['para_id'])) {
            $para = Para::findOne($post['para_id']);
            if (!isset($para)) {
                $errors['para_id'] = "Para Id is invalid";
            }
        } else {
            $errors['para_id'] =  "para_id is required";
        }

        if (isset($post['edu_year_id'])) {
            $eduYear = EduYear::findOne($post['edu_year_id']);
            if (!isset($eduYear)) {
                $errors['edu_year_id'] = "edu_year_id is invalid";
            }
        } else {
            $errors['edu_year_id'] =  "edu_year_id is required";
        }

        if (isset($post['week_id'])) {
            $week = Week::findOne($post['week_id']);
            if (!isset($week)) {
                $errors['week_id'] = "week id is invalid";
            }
        } else {
            $errors['week_id'] =  "week_id is required";
        }

        if (isset($post['semester_id'])) {
            $semester = Semestr::findOne($post['semester_id']);
            if (!isset($semester)) {
                $errors['semester_id'] = "semester_id is invalid";
            }
        } else {
            $errors['semester_id'] =  "semester_id is required";
        }

        if (count($errors) > 0) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        /**
         *  Ma'lumotlar to'g'ri jo'natilganligini tekshirish
         */

        $type = $semester->type;

        $semester_ids = Semestr::find()->select('id')->where(['type' => $type]);

        $roomIds =  TimeTable::find()
            ->select('room_id')
            ->where([
                'para_id' => $para->id,
                'edu_year_id' => $eduYear->id,
                'week_id' => $week->id,
                'is_deleted' => 0,
                'status' => 1,
                'archived' => 0

            ])->andWhere(['in', 'semester_id', $semester_ids]);


        $model = new Room();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        //sirtqilar uchun 
        // if (isset($roomIds)) {
        //     $query->andFilterWhere(['not in', 'id', $roomIds]);
        // }

        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {
        $model = new Room();

        $query = $model->find()
            ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            // ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            // ->andWhere(['tr.tabel_name' => 'faculty'])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

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
        $model = new Room();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Room::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Room::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Room::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Room::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Room::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
