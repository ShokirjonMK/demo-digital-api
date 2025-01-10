<?php

namespace api\resources;

use common\models\model\TelegramStudent;
use common\models\model\Timetable as Teg;
use Yii;

class Telegram extends Teg
{
    use ResourceTrait;

    public static function bot($telegramStudent , $telegram , $telegram_id , $text)
    {
        $step = $telegramStudent->step;
        $lang_id = $telegramStudent->lang_id;

        if ($step == 1) {

        }
        return 1;
    }



    public static function sendLang()
    {
        $textUz = "O'zingizga maqul tilni tanlang! Bot sizga ushbu tilda javob qaytaradi.";
    }


    public static function getSelectLanguageText($lang)
    {
        $array = [
            1 => "uz",
            2 => "en",
            3 => "ru",
        ];
        return isset($array[$lang]) ? $array[$lang] : null;
    }
    public static function getTranslateMessage($type, $lang_id)
    {
        $lang = self::getSelectLanguageText($lang_id);
        $array = [
            "" => [
                "uz" => "Qo'shimcha izox qoldiring...",
                "ru" => "Оставить комментарий...",
                "en" => "Leave a comment...",
            ],
        ];
        if (isset($array[$type])) {
            return isset($array[$type][$lang]) ? $array[$type][$lang] : $type;
        } else {
            return $type;
        }
    }
}
