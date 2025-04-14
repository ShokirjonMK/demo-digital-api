<?php

namespace api\controllers;

use common\models\model\Kafedra;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\UserAccess;

class KafedraController extends ApiActiveController
{
    public $modelClass = 'api\resources\Kafedra';

    public function actions()
    {
        return [];
    }

    public $table_name = 'kafedra';
    public $controller_name = 'Kafedra';

    const USER_ACCESS_TYPE_ID = 2;
    const ROLE = 'mudir';



    public function actionUserAccess($lang)
    {
        $post = Yii::$app->request->post();
        $result = UserAccess::createItems(self::USER_ACCESS_TYPE_ID, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Users successfully atached to ' . $this->controller_name), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
    public function actionIndex($lang)
    {
        $model = new Kafedra();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);



        if (!isRole('tutor')) {

            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query->andFilterWhere([
                    'faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
            }
            /*  is Self  */

            /*  is Self  */
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1 && !isRole("dean")) {

                // return $k['UserAccess']->table_id;
                $query->where([
                    'in', $this->table_name . '.id', $k['UserAccess']->table_id
                ])->all();
            } elseif ($k['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
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

    public function actionKpi($lang)
    {
        $model = new Kafedra();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);


        if (!(isRole('admin') || isRole('monitoring') || isRole('rector') || isRole('comission'))) {
            $query->andWhere(['user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data

        // dd([$query->createCommand()->rawSql, $t['table_ids']]);
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionCreate($lang)
    {
        $model = new Kafedra();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Kafedra::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Kafedra::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /* if ($this->checkLead($model, self::ROLE)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        } */

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Kafedra::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Kafedra::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Kafedra::find()
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
