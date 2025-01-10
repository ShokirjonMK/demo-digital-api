<?php
// Get current user
function current_user()
{
    return \Yii::$app->user->identity;
}

// Get current user id
function current_user_id()
{
    $user = \Yii::$app->user;
    $user_id = $user->getId();
    return is_numeric($user_id) ? $user_id : 0;
}

function current_student($user_id = null) {
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    if (is_numeric($user_id) && $user_id > 0) {
        return \common\models\model\Student::find()->where(['user_id' => $user_id, 'is_deleted' =>0])->one();
    }
}

// Get current user profile
function current_user_profile($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }

    if (is_numeric($user_id) && $user_id > 0) {
        return \common\models\model\Profile::find()->where(['user_id' => $user_id])->one();
    }
}

function get_mudir($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }
    $query = \common\models\model\Kafedra::findOne([
        'user_id' => $user_id,
        'status' => 1,
        'is_deleted' => 0
    ]);
    return $query;
}

function get_dean($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }
    $query = \common\models\model\Faculty::findOne([
        'user_id' => $user_id,
        'status' => 1,
        'is_deleted' => 0
    ]);
    return $query;
}

function get_dean_deputy($user_id = null)
{
    if (is_null($user_id)) {
        $user_id = current_user_id();
    }
    $query = \common\models\model\Faculty::findOne([
        'dean_deputy_user_id' => $user_id,
        'status' => 1,
        'is_deleted' => 0
    ]);
    return $query;
}

// Check user logged in
function is_user_logged_in()
{
    $isGuest = Yii::$app->user->isGuest;
    return $isGuest ? false : true;
}

// Get cart hash
function get_card_hash()
{
    $cookies = \Yii::$app->request->cookies;
    $card_hash = $cookies->getValue('card_hash');

    if (is_string($card_hash) && $card_hash) {
        return $card_hash;
    }
}
