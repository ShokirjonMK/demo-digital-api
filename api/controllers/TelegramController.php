<?php

namespace api\controllers;

use common\models\model\Profile;
use common\models\model\Telegram;
use common\models\User;
use GuzzleHttp\Client;
use Yii;
use yii\db\Expression;
use yii\web\Controller;

class TelegramController extends Controller
{
    use ApiOpen;

    public $modelClass = '';

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            // ...
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    // public $enableCsrfValidation = false;

    public function actionIndex()
    {

        // Yii::$app->controller->enableCsrfValidation = false;
        $telegram = Yii::$app->telegram;

        // $text = $telegram->input->message->text;
        // $username = $telegram->input->message->chat->username;
        // $chat_id = $telegram->input->message->chat->id;


        $t = $telegram->sendMessage([
            'chat_id' => 813225336,
            'text' => "Assalomu alaykum      asdasdasd ehhhhh",
        ]);

        // $t =  $telegram->sendMessage([
        //     'chat_id' => 676692104,
        //     'text' => "Assalomu alaykum "  . "Husniddin",
        // ]);

        return "asdasd";


        // if ($text == "/start") {

        //     $keyboards = json_encode([
        //         'keyboard' => [
        //             [
        //                 ['text' => "☎️Telefon raqamni jo`natish☎️", 'callback_data' => "/start"]
        //             ]
        //         ], 'resize_keyboard' => true
        //     ]);

        //     $telegram->sendMessage([
        //         'chat_id' => $chat_id,
        //         'text' => "Assalomu alaykum " . $username . " ",
        //         'reply_markup' => $keyboards
        //     ]);
        // }

        // if ($text == "☎️Telefon raqamni jo`natish☎️") {
        //     $replyMarkup3 = [
        //         'keyboard' => [[[
        //             'text' => 'Telefon raqamni jo`nating...',
        //             'request_contact' => true,
        //         ]]],
        //         'resize_keyboard' => true,
        //         'request_contact' => true,
        //     ];
        //     $encodedMarkup = json_encode($replyMarkup3);
        //     $telegram->sendMessage([
        //         'chat_id' => $chat_id,
        //         'text' => "Telefon raqamni jo`nating...",
        //         'reply_markup' => $encodedMarkup
        //     ]);
        //     die;
        // }

        // if (json_encode($telegram->input->message->contact) != "null") {
        //     $test = json_encode($telegram->input->message->contact);
        //     $new_test = json_decode($test);
        //     $phone = (int)$new_test->phone_number;

        //     $new_phone = "(" . mb_substr($phone, 3, 2) . ")-" . mb_substr($phone, 5, 3) . "-" . mb_substr($phone, 8, 4);

        //     $new_phone = preg_replace('/[^0-9]/', '', $new_phone);

        //     $student = Profile::find()
        //         ->select([
        //             new Expression("replace(replace(phone, '-', ''), ' ', '') as number"),
        //             new Expression("replace(replace(phone_secondary, '-', ''), ' ', '') as father_number"),
        //             // new Expression("replace(replace(mother_number, '-', ''), ' ', '') as mother_number"),
        //             'telegram_chat_id',
        //             'last_name',
        //             'first_name',
        //             'user_id',
        //         ])
        //         ->orWhere(['number' => $new_phone])
        //         ->orWhere(['father_number' => $new_phone])
        //         ->one();


        //     if ($student) {
        //         if ($student->telegram_chat_id) {
        //             $arr = explode("-", $student->telegram_chat_id);
        //             if (!in_array($chat_id, $arr)) {
        //                 $student->telegram_chat_id = $student->telegram_chat_id . "-" . $chat_id;
        //             }
        //         } else {
        //             $student->telegram_chat_id = json_encode($chat_id);
        //         }
        //         $student->save(false);

        //         $telegram->sendMessage([
        //             'chat_id' => $chat_id,
        //             'text' => $student->full_name . "-" . $new_phone
        //         ]);
        //     } else {
        //         $telegram->sendMessage([
        //             'chat_id' => $chat_id,
        //             'text' => "+998" . $new_phone . " raqamdan ro`yxatdan o`tgan o`quvchi topilmadi!!!"

        //         ]);
        //     }
        // }
    }

    //   $message_id = $telegram->input->message->message_id;
    // $full_name = $telegram->input->message->chat->first_name . ' ' . $telegram->input->message->chat->last_name;
    //   $username = $telegram->input->message->chat->username;

    public function actionBotPost()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $telegram = Yii::$app->telegram;
        $text = $telegram->input->message->text;
        $receivedMessageId = $telegram->input->message->message_id;
        $username = $telegram->input->message->chat->username;
        $chat_id = $telegram->input->message->chat->id;

        $hasUser = true;
        try {

            $user = User::find()
                ->andWhere(['telegram_chat_id' => $chat_id, 'deleted' => 0])
                ->one();
            $userOne = $user;

            $tg = Telegram::findByChatId($chat_id);
            if (!$tg) {
                $tg = new Telegram();
                $tg->telegram_username = $username;
                $tg->chat_id = $chat_id;
                $tg->lang_id = 1;
                $tg->lang = 'uz';
                $tg->step = 1;
                $tg->save(false);
            }
            if (!$user) {
                $hasUser = false;
            } else {
                $lang_id = $userOne->lang;
            }


            //ortga knopka uchun
            // if ($text == "🔙Назад" || $text == "🔙Orqaga" || $text == "🔙Back") {
            //     if ($hasUser) {
            //         if ($userOne->step < 3) {
            //             $text = '/start';
            //         } else {
            //             if ($userOne->step < 6) {
            //                 $userOne->step = 3;
            //                 $userOne->save(false);
            //                 return $telegram->sendMessage([
            //                     'chat_id' => $chat_id,
            //                     'text' => "🇺🇿\nTa'lim tilini tanlang.\n\n🇷🇺\nВыберите язык обучения",
            //                     'reply_markup' => self::getLanguages()
            //                 ]);
            //             } elseif ($userOne->step < 15) {
            //                 $userOne->step = 6;
            //                 $userOne->save(false);
            //                 return $telegram->sendMessage([
            //                     'chat_id' => $chat_id,
            //                     'text' => "🔘 *Qabul turini tanlang\\!*",
            //                     'parse_mode' => 'MarkdownV2',
            //                     'reply_markup' => json_encode([
            //                         'keyboard' => [
            //                             [
            //                                 ['text' => self::getTranslateMessage("Qabul 2024", $lang_id)],
            //                                 ['text' => self::getTranslateMessage("O‘qishni ko‘chirish", $lang_id)],
            //                             ],
            //                             [
            //                                 ['text' => self::undoKeyboardUser($user)]
            //                             ]
            //                         ],
            //                         'resize_keyboard' => true,
            //                     ])
            //                 ]);
            //             }
            //         }
            //     }
            // }


            if ($text == '/start') {
                if ($hasUser) {
                    $text = "Kerakli bo'limni tanlang 👇";

                    return $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' =>  $text,
                        'reply_markup' => self::getMenuTeacher()
                    ]);
                    // $second_chat_id = -813225336;
                    // $telegram->sendMessage([
                    //     'chat_id' => $second_chat_id,
                    //     'text' => $text,
                    //     'parse_mode' => 'MarkdownV2',
                    //     'reply_markup' => json_encode([
                    //         'remove_keyboard' => true
                    //     ])
                    // ]);
                    // return $telegram->sendMessage([
                    //     'chat_id' => $chat_id,
                    //     'text' => $text,
                    //     'parse_mode' => 'MarkdownV2',
                    //     'reply_markup' => json_encode([
                    //         'remove_keyboard' => true
                    //     ])
                    // ]);
                } else {
                    $tg->step = 2;
                    $tg->save(false);

                    return $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "🇺🇿Foydalanish tilini tanlang.\n\n🇺🇸Choose language. \n\n🇷🇺Выберите язык",
                        'reply_markup' => self::getLanguages()
                    ]);
                }
            }

            if ($tg->step == 2) {
                $selectedLangId = self::getSelectLanguage($text);
                if ($selectedLangId) {
                    $tg->lang = self::getSelectLanguageText($text);
                    $tg->lang_id = $selectedLangId;
                    $tg->step = 3;
                    $tg->save(false);

                    return $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Telefon raqamingizni yuboring\\.",
                        'parse_mode' => 'MarkdownV2',
                        'reply_markup' => json_encode([
                            'keyboard' => [[
                                [
                                    'text' => "☎️",
                                    'request_contact' => true,
                                ]
                            ]],
                            'resize_keyboard' => true,
                            'one_time_keyboard' => true,
                        ])
                    ]);
                }

                return $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => "🇺🇿Foydalanish tilini tanlang.\n\n🇺🇸Choose language. \n\n🇷🇺Выберите язык",
                    'reply_markup' => self::getLanguages()
                ]);
            }


            if ($tg->step == 3) {
                if (json_encode($telegram->input->message->contact) != "null") {
                    $contact = json_encode($telegram->input->message->contact);
                    $contact_new = json_decode($contact);
                    $phone = preg_replace('/[^0-9]/', '', $contact_new->phone_number);

                    $phoneKod = substr($phone, 0, 3);
                    if ($phoneKod != 998) {
                        return $telegram->sendMessage([
                            'chat_id' => $chat_id,
                            'text' => "⁉️⛔️ *Telefon raqamingiz noto'g'ri*",
                            'parse_mode' => 'MarkdownV2',
                            'reply_markup' => self::undoKeyboard($lang_id)
                        ]);
                    }

                    $tg->phone = "+" . $phone;
                    $tg->step = 4;
                    $tg->save(false);

                    return $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => 'Tizimdan foydalanishdagi username:'
                    ]);
                } else {
                    return $telegram->sendMessage([
                        'chat_id' => $chat_id,
                        'text' => "Telefon raqamingizni yuboring\\.",
                        'parse_mode' => 'MarkdownV2',
                        'reply_markup' => json_encode([
                            'keyboard' => [[
                                [
                                    'text' => "☎️",
                                    'request_contact' => true,
                                ]
                            ]],
                            'resize_keyboard' => true,
                            'one_time_keyboard' => true,
                        ])
                    ]);
                }
            }

            if ($tg->step == 4) {
                $tg->username = $text;
                $tg->step = 5;
                $tg->save(false);

                return $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => 'Tizimdan foydalanishdagi parol:'
                ]);
            }
            if ($tg->step == 5) {

                $tg->step = 6;
                $tg->save(false);

                //Authenticate user from $tg->username and $tg->password
                $userAuthenticated = User::authenticate($tg->username, $text);

                if ($userAuthenticated) {
                    $userAuthenticated->telegram_chat_id = $chat_id;
                    $userAuthenticated->lang = $tg->lang_id;
                    $userAuthenticated->save(false);

                    $msg = "User authenticated: " . $userAuthenticated->username;
                    $tg->step = 10;
                    $tg->user_id = $userAuthenticated->id;
                    $tg->save(false);
                } else {
                    $msg = "Login yoki parol xato. \n\n Tizimdan foydalanishdagi username:";
                    $tg->step = 4;
                    $tg->save(false);
                }


                return $telegram->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $msg,
                ]);
            }


            Yii::$app->telegram->sendMessage([
                'chat_id' => 813225336,
                'text' => json_encode($telegram->input),
            ]);

            /////


        } catch (\Exception $e) {
            return $telegram->sendMessage([
                'chat_id' => 813225336,
                'text' => $e->getMessage(),
            ]);
        } catch (\Throwable $t) {
            return $telegram->sendMessage([
                'chat_id' => 813225336,
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
            'IELTS nechida' => [
                "uz" => "IELTS nechida?",
                "ru" => "Сколько стоит IELTS?",
                "en" => "Your IELTS score?",
            ],
            'Siz bakalavrgami yoki magistrgami?' => [
                "uz" => "Siz bakalavrgami yoki magistrgami?",
                "ru" => "Вы бакалавр или магистр?",
                "en" => "Are you a bachelor or master?",
            ],
            "Xato format" =>
            [
                "uz" => "Xato format",
                "ru" => "Формат ошибки",
                "en" => "Error format",
            ],
            "Qabul 2024" =>
            [
                "uz" => "Qabul 2024",
                "ru" => "Прием 2024 г.",
                "en" => "Admission 2024",
            ],
            "O‘qishni ko‘chirish" =>
            [
                "uz" => "O‘qishni ko‘chirish",
                "ru" => "Перевод",
                "en" => "Transfer of study",
            ],
            "Qabul turini tanlang..." =>
            [
                "uz" => "Qabul turini tanlang...",
                "ru" => "Выберите тип приема...",
                "en" => "Select the type of reception...",
            ],
            "Kunduzgi" =>
            [
                "uz" => "Kunduzgi",
                "ru" => "Очное",
            ],
            "Kechgi" =>
            [
                "uz" => "Kechgi",
                "ru" => "Вечер",
            ],
            "Sirtqi" =>
            [
                "uz" => "Sirtqi",
                "ru" => "Заучный",
            ],
            "📞 Telefon raqamingizni yuboring 📞" =>
            [
                "uz" => "📞Telefon raqamingizni yuboring📞",
                "ru" => "📞Отправьте свой номер телефона📞",
                "en" => "📞Send your phone number📞",
            ],
            'Yoshiz nechida' => [
                "uz" => "Yoshiz nechida?",
                "ru" => "Сколько тебе лет?",
                "en" => "How old are you?",
            ],
            "Tasdiqlash kodini kiriting..." =>
            [
                "uz" => "Tasdiqlash kodini kiriting...",
                "ru" => "Введите код подтверждения...",
                "en" => "Enter confirmation code...",
            ],
            "Tasdiqlash kodi noto'g'ri. Iltimos tasdiqlash kodini qayta kiriting." =>
            [
                "uz" => "Tasdiqlash kodi noto'g'ri. Iltimos tasdiqlash kodini qayta kiriting.",
                "ru" => "Код подтверждения неверный. Пожалуйста, введите код подтверждения еще раз.",
                "en" => "The confirmation code is incorrect. Please enter the confirmation code again.",
            ],
            "Qaysi viloyatda yashaysiz" =>
            [
                "uz" => "Qaysi viloyatda yashaysiz?",
                "ru" => "В какой области проживаете?",
                "en" => "Which province do you live in?",
            ],
            "Qaysi tumanda yashaysiz?" =>
            [
                "uz" => "Qaysi tumanda yashaysiz?",
                "ru" => "В какой pайон проживаете?",
                "en" => "Which district do you live in?",
            ],
            "Manzil qiymati 10 tadan ko'p bo'lsin..." =>
            [
                "uz" => "Manzil qiymati 10 tadan ko'p bo'lsin...",
                "ru" => "Пусть значение адреса больше 10...",
                "en" => "Let the address value be more than 10 ...",
            ],
            "Tabriklaymiz, siz ro’yxatdan muvaffaqiyatli o’tdingiz Kerakli bo'limni tanlang!!!" =>
            [
                "uz" => "Tabriklaymiz, siz ro’yxatdan muvaffaqiyatli o’tdingiz Kerakli bo'limni tanlang!!!",
                "ru" => "Поздравляем, вы успешно зарегистрировались. Выберите нужный раздел!!!",
                "en" => "Congratulations, you have successfully registered. Select the desired section !!!",
            ],
            "Yashash manzilingizni yozing..." =>
            [
                "uz" => "Yashash manzilingizni yozing...",
                "ru" => "Введите свой адрес...",
                "en" => "Enter your address...",
            ],
            //"" =>
            //                [
            //                    "uz" => "",
            //                    "ru" => "",
            //                    "en" => "",
            //                ],
            //"" =>
            //                [
            //                    "uz" => "",
            //                    "ru" => "",
            //                    "en" => "",
            //                ],


        ];
        if (isset($array[$text])) {
            return isset($array[$text][$lang]) ? $array[$text][$lang] : $text;
        } else {
            return $text;
        }
    }

    public static function getLanguages()
    {
        return json_encode([
            'keyboard' => [
                [
                    ['text' => "🇺🇿O‘zbek🇺🇿"],
                    ['text' => "🇺🇸English🇺🇸"],
                    ['text' => "🇷🇺Русский🇷🇺"],
                ],
            ], 'resize_keyboard' => true
        ]);
    }

    public static function getMenuTeacher()
    {
        return json_encode([
            'keyboard' => [
                [
                    ['text' => "Dars jadvalim"],
                    ['text' => "Tekshirilmagan ishlarim"],
                    ['text' => "Sozlamalar"],
                ],
            ], 'resize_keyboard' => true
        ]);
    }

    public static function getSelectLanguage($lang)
    {
        if (($lang == '🇺🇿O‘zbek🇺🇿')) {
            return 1;
        }
        if (($lang == '🇺🇸English🇺🇸')) {
            return 3;
        }
        if (($lang == '🇷🇺Русский🇷🇺')) {
            return 3;
        }
        return false;
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

    public static function undoKeyboard($lang_id)
    {
        if ($lang_id == 2) {
            $text_keybord_undo = "🔙Back";
        } elseif ($lang_id == 3) {
            $text_keybord_undo = "🔙Назад";
        } else {
            $text_keybord_undo = "🔙Orqaga";
        }
        $keyboard_basic_undo = json_encode([
            'keyboard' => [
                [
                    ['text' => $text_keybord_undo]
                ]
            ], 'resize_keyboard' => true
        ]);
        return $keyboard_basic_undo;
    }


    public static function undoKeyboardUser($user)
    {
        if ($user->lang_id == 3) {
            $text_keybord_undo = "🔙Back";
        } elseif ($user->lang_id == 2) {
            $text_keybord_undo = "🔙Back";
        } else {
            $text_keybord_undo = "🔙Orqaga";
        }
        return $text_keybord_undo;
    }

    public function getLanguageKeyboards()
    {
        $keyboard = [
            [
                ['text' => 'O‘zbekcha', 'callback_data' => 'uz'],
                ['text' => 'Русский', 'callback_data' => 'en'],
                ['text' => 'English', 'callback_data' => 'ru'],
            ]
        ];

        return json_encode([
            'keyboard' => $keyboard
        ]);
    }

    public function getKeybords()
    {
        // Create an inline keyboard markup
        $keyboard = [
            [
                ['text' => 'Kirish', 'callback_data' => 'login'],
            ]
        ];

        // Convert keyboard array to JSON
        return json_encode([
            'inline_keyboard' => $keyboard
        ]);
    }


    public function actionBot()
    {


        // Yii::$app->controller->enableCsrfValidation = false;
        $telegram = Yii::$app->telegram;

        // $text = $telegram->input->message->text;
        // $username = $telegram->input->message->chat->username;
        // $chat_id = $telegram->input->message->chat->id;


        $t = $telegram->sendMessage([
            'chat_id' => 813225336,
            'text' => "Assalomu alaykum " . "ehhhhh",
        ]);
        // $t =  $telegram->sendMessage([
        //     'chat_id' => 676692104,
        //     'text' => "Assalomu alaykum "  . "Husniddin",
        // ]);

        return "sss";


        // if ($text == "/start") {

        //     $keyboards = json_encode([
        //         'keyboard' => [
        //             [
        //                 ['text' => "☎️Telefon raqamni jo`natish☎️", 'callback_data' => "/start"]
        //             ]
        //         ], 'resize_keyboard' => true
        //     ]);

        //     $telegram->sendMessage([
        //         'chat_id' => $chat_id,
        //         'text' => "Assalomu alaykum " . $username . " ",
        //         'reply_markup' => $keyboards
        //     ]);
        // }

        // if ($text == "☎️Telefon raqamni jo`natish☎️") {
        //     $replyMarkup3 = [
        //         'keyboard' => [[[
        //             'text' => 'Telefon raqamni jo`nating...',
        //             'request_contact' => true,
        //         ]]],
        //         'resize_keyboard' => true,
        //         'request_contact' => true,
        //     ];
        //     $encodedMarkup = json_encode($replyMarkup3);
        //     $telegram->sendMessage([
        //         'chat_id' => $chat_id,
        //         'text' => "Telefon raqamni jo`nating...",
        //         'reply_markup' => $encodedMarkup
        //     ]);
        //     die;
        // }

        // if (json_encode($telegram->input->message->contact) != "null") {
        //     $test = json_encode($telegram->input->message->contact);
        //     $new_test = json_decode($test);
        //     $phone = (int)$new_test->phone_number;

        //     $new_phone = "(" . mb_substr($phone, 3, 2) . ")-" . mb_substr($phone, 5, 3) . "-" . mb_substr($phone, 8, 4);

        //     $new_phone = preg_replace('/[^0-9]/', '', $new_phone);

        //     $student = Profile::find()
        //         ->select([
        //             new Expression("replace(replace(phone, '-', ''), ' ', '') as number"),
        //             new Expression("replace(replace(phone_secondary, '-', ''), ' ', '') as father_number"),
        //             // new Expression("replace(replace(mother_number, '-', ''), ' ', '') as mother_number"),
        //             'telegram_chat_id',
        //             'last_name',
        //             'first_name',
        //             'user_id',
        //         ])
        //         ->orWhere(['number' => $new_phone])
        //         ->orWhere(['father_number' => $new_phone])
        //         ->one();


        //     if ($student) {
        //         if ($student->telegram_chat_id) {
        //             $arr = explode("-", $student->telegram_chat_id);
        //             if (!in_array($chat_id, $arr)) {
        //                 $student->telegram_chat_id = $student->telegram_chat_id . "-" . $chat_id;
        //             }
        //         } else {
        //             $student->telegram_chat_id = json_encode($chat_id);
        //         }
        //         $student->save(false);

        //         $telegram->sendMessage([
        //             'chat_id' => $chat_id,
        //             'text' => $student->full_name . "-" . $new_phone
        //         ]);
        //     } else {
        //         $telegram->sendMessage([
        //             'chat_id' => $chat_id,
        //             'text' => "+998" . $new_phone . " raqamdan ro`yxatdan o`tgan o`quvchi topilmadi!!!"

        //         ]);
        //     }
        // }
    }

    public function actionIndexFace()
    {
        $data = [];
        $error = [];
        $data['status'] = false;

        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . '80fb263fb349f7fe73ebf2d53a4fe16666706387',
                //                'Authorization' => 'Bearer ' . self::getAuthToken(),
            ]
        ]);

        $response = $client->post(
            'http://192.168.100.77:8005/check-face',
            ['body' => json_encode(
                [
                    "origin_image" => "https://api-digital.tsul.uz/storage/user_images_new/40102941670048.png",
                    "real_image" => "https://api-digital.tsul.uz/storage/user_images_new/43110976520017.png"
                ]
            )]
        );

        $res = json_decode($response->getBody()->getContents());


        //        $data = [];
        //        $error = [];
        //        $data['status'] = false;
        //
        //
        //        $client = new Client([
        //            'headers' => [
        //                'Content-Type' => 'application/json',
        //            ]
        //        ]);
        //
        //        $response = $client->post(
        //            'http://192.168.100.77:8005/check-face',
        //            ['body' => json_encode(
        //                [
        //                    "origin_image" => "https://api-digital.tsul.uz/storage/user_images_new/40102941670048.png",
        //                    "real_image" => "https://api-digital.tsul.uz/storage/user_images_new/43110976520017.png"
        //                ]
        //            )]
        //        );
        //
        //        $res = json_decode($response->getBody()->getContents());


        dd($res);
    }
}
