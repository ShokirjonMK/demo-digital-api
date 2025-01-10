<?php

namespace api\controllers;

use common\models\model\Department;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\LoadRate;
use common\models\model\TeacherAccess;
use common\models\model\UserAccessType;
use Yii;
use base\ResponseStatus;
use common\models\model\UserAccess;

class UserAccessController extends ApiActiveController
{
    public $modelClass = 'api\resources\UserAccess';

    const FACULTY = 1;
    const KAFEDRA = 2;
    const DEPARTMENT = 3;

    public function actions()
    {
        return [];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public $table_name = 'user_access';
    public $controller_name = 'UserAccess';

    public function actionIndex($lang)
    {
        $model = new UserAccess();

        $query = $model->find()
            ->where([
                'status' => 1,
                'is_deleted' => 0,
            ]);
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
        $model = new UserAccess();
        $post = Yii::$app->request->post();
        $post['is_leader'] = isset($post['is_leader']) ? $post['is_leader'] : 0;
        if ($post['is_leader'] < 0 || $post['is_leader'] > 1) {
            $post['is_leader'] = 0;
        }
        $this->load($model, $post);
        $result = UserAccess::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($lang, $id)
    {
        $model = UserAccess::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = LoadRate::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = LoadRate::deleteItem($model);

        if (!is_array($result)) {
            return $this->response(1, _e( 'Load Rate successfully removed.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
