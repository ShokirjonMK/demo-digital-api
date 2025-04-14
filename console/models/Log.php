<?php

namespace console\models;

use Yii;
use yii\base\Model;

class Log extends Model
{
    public static function write($message, $category = 'default')
    {
        if (Yii::$app->params['mkStatusLogging']) {
            Yii::info($message, $category);
        }
    }
}
