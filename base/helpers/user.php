<?php
// Get current user
function current_user()
{
    return \Yii::$app->user->identity;
}

// Get current user id
function current_user_id()
{
    $user = \Yii::$app->user ?? null;
    if (!$user) {
        return 0;
    }
    $user_id = $user->getId();
    return is_numeric($user_id) ? $user_id : 0;
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
