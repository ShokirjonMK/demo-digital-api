<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Poll;
use common\models\model\Translate;
use yii\db\Expression;

class PollController extends ApiActiveController
{
    public $modelClass = 'api\resources\poll';

    public function actions()
    {
        return [];
    }

    public $table_name = 'poll';
    public $controller_name = 'Poll';

    public function actionIndex($lang)
    {
        $model = new Poll();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")

            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (!isRole('admin')) {
            // Use a custom SQL expression to check for JSON overlap
            // jsonfilter
            $query->andWhere(new Expression('JSON_OVERLAPS(:roles, roles)', [':roles' => json_encode(current_user_roles_array())]));
        }


        if (isRole('student')) {
            // $query->andWhere(['type' => $this->student(2)->edu_type_id]);
            $query->andWhere([
                'or',   
                ['type' => $this->student(2)->edu_type_id],
                ['type' => null]
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        // $query = $this->sort($query);
        $query->orderBy(['order' => SORT_ASC]);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Poll();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Poll::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Poll::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Poll::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Poll::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Poll::find()
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

    public function actionType()
    {
        return $this->response(1, _e('Success.'), Poll::typeList(), null, ResponseStatus::OK);
    }
}
