<?php

namespace api\controllers;

use aki\telegram\base\Command;
use api\resources\Telegram;
use common\models\model\TelegramStudent;
use Yii;
use base\ResponseStatus;
use api\forms\Login;
use common\models\model\LoginHistory;
use common\models\model\StudentTimeTable;
use yii\httpclient\Client;

class IkStudentBotController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['permissionCheck']);
        unset($behaviors['authorCheck']);
        return $behaviors;
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionBot()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $telegram = Yii::$app->telegram;
        $text = $telegram->input->message->text;
        $username = $telegram->input->message->chat->username;
        $telegram_id = $telegram->input->message->chat->id;

        try {

            $telegramStudent = TelegramStudent::findOne([
                'chat_id' => $telegram_id,
                'is_deleted' => 0
            ]);

            if (!$telegramStudent) {
                $telegramStudent = new TelegramStudent();
                $telegramStudent->chat_id = $telegram_id;
                $telegramStudent->username = $username;
                $telegramStudent->step = 1;
                $telegramStudent->lang_id = 1;
                $telegramStudent->status = 1;
                $telegramStudent->save(false);
            } else {
                $telegramStudent->username = $username;
                $telegramStudent->update(false);
            }

            $result = Telegram::bot($telegramStudent , $telegram , $telegram_id , $text);

        } catch (\Exception $e) {
            return $telegram->sendMessage([
                'chat_id' => 1841508935,
                'text' => $e->getMessage(),
            ]);
        } catch (\Throwable $t) {
            return $telegram->sendMessage([
                'chat_id' => 1841508935,
                'text' => $t->getMessage(), " at ", $t->getFile(), ":", $t->getLine(),
            ]);
        }
    }

    public static function getTranslateMessage($text, $lang_id)
    {
        $lang = self::getSelectLanguageText($lang_id);
        $array = [
            "Qo'shimcha izox qoldiring..." => [
                "uz" => "Qo'shimcha izox qoldiring...",
                "ru" => "Оставить комментарий...",
                "en" => "Leave a comment...",
            ],
        ];
        if (isset($array[$text])) {
            return isset($array[$text][$lang]) ? $array[$text][$lang] : $text;
        } else {
            return $text;
        }
    }
}