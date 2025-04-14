<?php

namespace api\controllers;

use api\resources\AccessControl;
use api\resources\AuthItem;
use base\ResponseStatus;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use Yii;

class AccessControlController extends ApiActiveController
{
    public $modelClass = 'api\resources\AuthItem';

    public function actions()
    {
        return [];
    }

    public function actionRoles()
    {
        $model = new AuthChild();

        $user_id = current_user_id();

        if (isRole('admin')) {
            $roles = new AuthItem();
            $queryRole = $roles->find()
                ->where(['type' => 1])
                ->andWhere(['!=', 'name', 'admin'])
                // ->andFilterWhere(['like', 'child', Yii::$app->request->get('query')])
            ;

            // sort
            $queryRole = $this->sort($queryRole);

            // data
            $data =  $this->getDataNoPage($queryRole);

            return $this->response(1, _e('Success'), $data);
        }

        $getMyRoles = AuthAssignment::find()->select("item_name")->where([
            'user_id' => $user_id
        ]);

        $query = $model->find()
            ->where(['in', 'parent', $getMyRoles])
            ->andFilterWhere(['like', 'child', Yii::$app->request->get('query')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionRolePermissions($role)
    {
        $model = new AuthItem();

        $data = $model->find()
            ->where(['name' => $role])
            ->one();

        if ($data) {
            return $this->response(1, _e('Success'), $data);
        } else {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
    }

    public function actionPermissions()
    {
        $model = new AuthItem();

        $query = $model->find()
            ->where(['type' => AuthItem::TYPE_PERMISSION])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  AuthItem::getData($query);

        if ($data) {
            return $this->response(1, _e('Success'), $data);
        } else {
            return $this->response(0, _e('Data not found'), null, null);
        }
    }

    public function actionCreateRole()
    {
        $body = Yii::$app->request->rawBody;

        $result = AuthItem::createRole($body);
        if (!is_array($result)) {
            return $this->response(1, _e('New role(s) successfully created with given permissions.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdateRole()
    {
        $body = Yii::$app->request->rawBody;

        $result = AuthItem::updateRole($body);
        if (!is_array($result)) {
            return $this->response(1, _e('Role(s) successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDeleteRole($role)
    {
        $result = AuthItem::deleteRole($role);
        if (!is_array($result)) {
            return $this->response(1, _e('Role successfully removed.'), null, null, ResponseStatus::NO_CONTENT);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}
