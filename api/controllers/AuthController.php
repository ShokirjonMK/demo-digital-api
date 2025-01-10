<?php

namespace api\controllers;

use aki\telegram\base\Command;
use Yii;
use base\ResponseStatus;
use api\forms\Login;
use common\models\model\LoginHistory;
use common\models\model\StudentTimeTable;
use yii\httpclient\Client;

class AuthController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['permissionCheck']);
        unset($behaviors['authorCheck']);
        return $behaviors;
    }

    public function actionLogin()
    {
        $post = Yii::$app->request->post();
        if (isset($post['is_main'])) {
            if ($post['is_main'] == 1) {
                $result = Login::loginMain(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        // return $result['data']['role'];
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                     $res = LoginHistory::createItemLogin($result['data']['user_id']);
                     if (!is_array($res)) {
                        return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                     }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], _e('Login not saved'));
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            } elseif ($post['is_main'] == 0) {
                $result = Login::loginStd(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        // return $result['data']['role'];
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }

                     $res = LoginHistory::createItemLogin($result['data']['user_id']);
                     if (!is_array($res)) {



                    // /** Kurs bo'yicha vaqt belgilash */
                    // $errors = [];
                    // if (!StudentTimeTable::chekTime()) {
                    //     $errors[] = _e('This is not your time to choose!');
                    //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
                    // }
                    // /** Kurs bo'yicha vaqt belgilash */


                    return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                     }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], _e('Login not saved'));
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            }
        } else {
            /* olib tashlash sharti bilan */

            $result = Login::login(new Login(), $post);
            if ($result['is_ok']) {
                 $res = LoginHistory::createItemLogin($result['data']['user_id']);
                 if (!is_array($res)) {
                return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                 }

                return $this->response(1, _e('User successfully logged in.'), $result['data'], _e('Login not saved'));
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
            }
            /* olib tashlash sharti bilan */

            return $this->response(0, _e('Something went wrong!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $result = Login::login(new Login(), $post);
        if ($result['is_ok']) {
            $res = LoginHistory::createItemLogin($result['data']['user_id']);
            if (!is_array($res)) {
                return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
            }
            return $this->response(1, _e('User successfully logged in.'), $result['data'], _e('Login not saved'));
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
        }
    }

    public function actionBot()
    {
        return getIpMK();
    }

}