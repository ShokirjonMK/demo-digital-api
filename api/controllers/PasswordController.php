<?php

namespace api\controllers;

use Yii;
use api\resources\User;
use api\resources\Password;
use base\ResponseStatus;
use common\models\model\AuthChild;
use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\PasswordEncrypts;

class PasswordController extends ApiActiveController
{
    public $modelClass = 'api\resources\Password';

    // public $modelClass;

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $data = new Password();
        $data = $data->decryptThisUser();
        return $this->response(1, _e('Success'), $data);
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();

        $passwordNew =  $post['new_password'] ?? null;
        $passwordOld =  $post['old_password'] ?? null;
        $passwordRe =  $post['re_password'] ?? null;
        $data = new Password();

        if (isRole('admin')) {
            $data = $data->decryptThisUser($id);
        } else {
            $data = $data->decryptThisUser(current_user_id());
        }
        if (($data['password'] == $passwordOld) || isRole('admin')) {

            if (strlen($passwordNew) >= 6) {

                if ($passwordRe == $passwordNew) {
                    if (isRole('admin') && current_user_id() != $id) {
                        $model = User::findOne($id);
                        $model->savePassword($passwordNew, $id);
                        $model->is_changed = User::PASSWORD_NO_CHANED;
                    } else {
                        $model = User::findOne(current_user_id());
                        $model->savePassword($passwordNew, current_user_id());
                        $model->is_changed = User::PASSWORD_CHANED;
                    }
                    //**parolni shifrlab saqlaymiz */
                    // $model->savePassword($passwordNew, current_user_id());
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($passwordNew);


                    if ($model->save()) {
                        return $this->response(1, _e('Password successfully changed!'), null, null, ResponseStatus::OK);
                    } else {
                        return $this->response(0, _e('There is an error occurred while changing password!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                    }
                } else {
                    return $this->response(0, _e('Passwords are not same.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('The password must be at least 6 characters.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('Old password incorrect.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    // public function actionViews($lang, $id)
    // {
    //     $get_user_id = $id;

    //     if (current_user_id() != $get_user_id) {
    //         $isChild =
    //             AuthChild::find()
    //             ->where(['in', 'child', user_roles_array($get_user_id)])
    //             ->andWhere(['in', 'parent', current_user_roles_array()])
    //             ->all();

    //         if (!$isChild) return $this->response(0, _e('You can not get .'), null, null, ResponseStatus::NOT_FOUND);
    //     }

    //     if (!isRole('student', $get_user_id)) return $this->response(0, _e('You can not get.'), null, null, ResponseStatus::NOT_FOUND);

    //     $data = new Password();
    //     $data = $data->decryptThisUser($get_user_id);

    //     return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
    //     return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    // }


    // Action to view user details
    public function actionView($lang, $id)
    {
        $get_user_id = $id;

        // Check if the current user is different from the target user
        if (current_user_id() != $get_user_id) {
            // Query the AuthChild table to find records where the target user's role is a child role of the current user's role


            // If no records are found, the current user does not have the necessary roles to access the information
            if (!(AuthChild::find()
                ->where(['child' => user_roles_array($get_user_id)])
                ->andWhere(['parent' => current_user_roles_array()])
                ->exists())) {
                return $this->response(0, _e('You cannot access this informatio!.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        // Check if the user has the 'student' role
        // if (!isRole("admin"))
        //     if (!in_array('student', user_roles_array($get_user_id))) {
        //         return $this->response(0, _e('You cannot access this information.'), null, null, ResponseStatus::NOT_FOUND);
        //     }

        // Decrypt and return user data
        $data = (new Password())->decryptThisUser($get_user_id);

        // Return success response
        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
    }
}
