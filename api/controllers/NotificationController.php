<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Notification;
use common\models\model\NotificationRole;
use common\models\model\NotificationRoleName;
use common\models\model\NotificationUser;
use common\models\model\Student;

class NotificationController extends ApiActiveController
{
    public $modelClass = 'api\resources\Notification';

    public function actions()
    {
        return [];
    }

    public $table_name = 'notification';
    public $controller_name = 'Notification';

    public function actionMy($lang)
    {
        $model = new Notification();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            //faqat o'zi yaratgan notiflarni berish
            ->andWhere([$this->table_name . '.created_by' => current_user_id()])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);


        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex($lang)
    {
        $model = new Notification();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->leftJoin("notification_role", "notification_role.notification_id = $this->table_name.id")
            ->leftJoin("notification_user", "notification_user.notification_id = $this->table_name.id and notification_user.user_id =" . current_user_id())
            ->andWhere(['notification_user.notification_id' => null])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $query->andWhere(['in', 'notification_role.role', current_user_roles_array()]);

        if (null !== Yii::$app->request->get('my')) {
            $query->andWhere([$this->table_name . '.created_by' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    // public function actionIndex1($lang)
    // {
    //     $current_user_id = current_user_id();
    //     $model = new NotificationRole();
    //     $table = 'notification_role';
    //     $tableUser = 'notification_user';

    //     $notification_user = NotificationUser::find()->select('notification_id')->where(['user_id' => $current_user_id]);

    //     $notification_role_user = $model->find()
    //         ->andWhere([$this->table_name . '.is_deleted' => 0])
    //         ->leftJoin($this->table_name, "$table.notification_id = $this->table_name.id ")
    //         ->andWhere(['in', $table . '.role', current_user_roles_array()])
    //         ->andWhere(['not in', $table . '.notification_id', $notification_user])
    //         ->all();

    //     foreach ($notification_role_user as $nruOne) {

    //         if (_checkRole('student') && current_user_is_this_role($nruOne->created_by, 'tutor')) {

    //             $nowStudent = Student::findOne($current_user_id);
    //             if ($nowStudent) {
    //                 if ($nowStudent->tutor_id == $nruOne->created_by) {
    //                     $notificationUserNew = new NotificationUser();
    //                     $notificationUserNew->notification_id = $nruOne->notification_id;
    //                     $notificationUserNew->notification_role_id = $nruOne->id;
    //                     $notificationUserNew->user_id = $current_user_id;
    //                     $notificationUserNew->status = NotificationUser::STATUS_ACTIVE;
    //                     $notificationUserNew->save();
    //                 }
    //             }
    //         } else {
    //             $notificationUserNew = new NotificationUser();
    //             $notificationUserNew->notification_id = $nruOne->notification_id;
    //             $notificationUserNew->notification_role_id = $nruOne->id;
    //             $notificationUserNew->user_id = $current_user_id;
    //             $notificationUserNew->status = NotificationUser::STATUS_ACTIVE;
    //             $notificationUserNew->save();
    //         }
    //     }

    //     if (Yii::$app->request->get('all') == 1) {
    //         $query = NotificationUser::find()
    //             // ->andWhere(['status' => 1])
    //             ->andWhere(['user_id' => $current_user_id])
    //             // ->all()
    //         ;
    //     } else {
    //         $query = NotificationUser::find()
    //             ->andWhere(['status' => 1])
    //             ->andWhere(['user_id' => $current_user_id])
    //             // ->all()
    //         ;
    //     }

    //     // filter
    //     $query = $this->filterAll($query, $model);

    //     // sort
    //     $query = $this->sort($query);

    //     // data
    //     $data =  $this->getData($query);

    //     return $this->response(1, _e('Success'), $data);
    // }

    public function actionCreate($lang)
    {
        $model = new Notification();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Notification::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionApproved($lang, $id)
    {
        $model = Notification::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = NotificationUser::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully approved.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (isset($post['status'])) {
            if ((array_key_exists($post['status'], NotificationUser::statusList()))) {
                $model->status = (int)$post['status'];
                if ($model->save()) {
                    return $this->response(1, _e($this->controller_name . ' successfully approved.'), $model, null, ResponseStatus::OK);
                }
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionUpdate($lang, $id)
    {
        $model = Notification::findOne(['id' => $id, 'created_by' => current_user_id()]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Notification::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Notification::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->andWhere(['created_by' => Current_user_id()])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }


    /**
     * Deletes a notification.
     *
     * @param string $lang Language parameter.
     * @param int $id The ID of the notification to delete.
     * @return Response The response object.
     */
    public function actionDelete($lang, $id)
    {
        // Fetch the notification model based on the ID and user role.
        $query = Notification::find()->where(['id' => $id, 'is_deleted' => 0]);
        if (!isRole('admin')) {
            $query->andWhere(['created_by' => Current_user_id()]);
        }
        $model = $query->one();

        // If model doesn't exist, return a not found response.
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // Delete the notification.
        $result = Notification::deleteItem($model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully deleted.'));
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    // public function actionDelete12($lang, $id)
    // {
    //     $query = Notification::find()
    //         ->where(['id' => $id, 'is_deleted' => 0]);

    //     if (!isRole('admin')) {
    //         $query->andWhere(['created_by' => Current_user_id()]);
    //     }

    //     $model = $query->one();

    //     if (!$model) {
    //         return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    //     }

    //     // remove model
    //     if ($model) {
    //         $result = Notification::deleteItem($model);
    //         if (!is_array($result)) {
    //             return $this->response(1, _e($this->controller_name . ' successfully deleted.'));
    //         } else {
    //             return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    //         }

    //         return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
    //     }
    //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    // }

    public function actionStatusList()
    {
        return $this->response(1, _e('Success.'), NotificationUser::statusList(), null, ResponseStatus::OK);
    }
}
