<?php

namespace console\controllers;


use Yii;
use yii\console\Controller;

class TelegramController extends Controller
{
    public function actionStart()
    {
        $telegram = Yii::$app->telegram;
//        $telegram_id = $telegram->input->message->chat->id;
        $telegram_id = 1841508935;
        $text = $telegram->input->message->text;

//        $telegram->sendMessage([
//            'chat_id' => $telegram_id,
//            'text' => 'Lokalda ketti',
//            'reply_markup' => json_encode([
//                'inline_keyboard'=>[
//                    [
//                        ['text'=>"refresh",'callback_data'=> time()]
//                    ]
//                ]
//            ]),
//        ]);

        if ($text == "/start") {
            $telegram->sendMessage([
                'chat_id' => $telegram_id,
                'text' => 'Lokalda ketti1111111',
//                'reply_markup' => self::getKeybords()
            ]);
        }
    }
}