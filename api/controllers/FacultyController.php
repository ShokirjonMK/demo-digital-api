<?php

namespace api\controllers;

use api\resources\AccessControl;
use api\resources\User;
use common\models\model\Kafedra;
use common\models\model\TimeTable1;
use common\models\model\TimetableDate;
use Yii;
use base\ResponseStatus;
use yii\web\ForbiddenHttpException;

use common\models\model\Faculty;
use common\models\model\Translate;
use common\models\model\UserAccess;

use function PHPSTORM_META\type;

class FacultyController extends ApiController
{

    public function actions()
    {
        return [];
    }

    public $table_name = 'faculty';
    public $controller_name = 'Faculty';

    const USER_ACCESS_TYPE_ID = 1;
    const ROLE = 'dean';

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
        $model = new Faculty();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
//            ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // is Self 

        // if (isRole('justice')) {
        // }

        if (isRole('teacher')) {
            $query->andWhere(['in' , $this->table_name .'.id' , TimetableDate::find()
                ->select('faculty_id')
                ->where([
                    'user_id' => current_user_id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ])]);
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query->andWhere([
                    $this->table_name . '.id' => $t['UserAccess']
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty.is_deleted' => -1
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
        $model = new Faculty();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Faculty::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Faculty::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /* if ($this->checkLead($model, self::ROLE)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        } */

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Faculty::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Faculty::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Faculty::find()
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
