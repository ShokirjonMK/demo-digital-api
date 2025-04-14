<?php

namespace api\controllers;

use Yii;
use api\forms\EmployeeSubject;
use api\resources\EmployeeUser;
use api\resources\Profile;
use common\models\Employee as CommonEmployee;
use base\ResponseStatus;

class EmployeeController extends ApiActiveController
{
    public $modelClass = 'api\resources\EmployeeUser';

    public function actions()
    {
        return [];
    }

    public function actionIndex()
    {
        $model = new EmployeeUser();

        $query = $model->find()
            ->with(['profile'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->andWhere(['in', 'auth_assignment.item_name', EmployeeUser::$roleList])
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        
        // data
        $data =  $this->getData($query);
        
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        $model = new EmployeeUser();
        $profile = new Profile();
        $employee = new CommonEmployee();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($employee, $post);
        
        $result = EmployeeUser::createItem($model, $profile, $employee, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Employee successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($id)
    {
        $model = EmployeeUser::findEmployee($id);
        if(!$model || !$model->employee || !$model->profile){
            return $this->response(0, _e('Employee not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $profile = $model->profile;
        $employee = $model->employee;
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $this->load($employee, $post);
        $result = EmployeeUser::updateItem($model, $profile, $employee, $post);
        if(!is_array($result)){
            return $this->response(1, _e('Employee successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($id)
    {
        $model = EmployeeUser::findEmployee($id);
        if(!$model || !$model->employee || !$model->profile){
            return $this->response(0, _e('Employee not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($id)
    {
        $result = EmployeeUser::deleteItem($id);
        if(!is_array($result)){
            return $this->response(1, _e('Employee successfully deleted.'), null, null, ResponseStatus::NO_CONTENT);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }

        
    }

    public function actionBindSubject($employee_id)
    {
        $body = Yii::$app->request->rawBody;

        $model = new EmployeeSubject();
        $model->user_id = $employee_id;
        $result = EmployeeUser::bindSubject($model, $body);
        if (!is_array($result)) {
            return $this->response(1, _e('Subjects successfully binded to teacher.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionSubjects($employee_id)
    {

        $result = EmployeeUser::getSubjects($employee_id);
        if ($result['is_ok']) {
            return $this->response(1, _e('Success.'), $result['data'], null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

}
