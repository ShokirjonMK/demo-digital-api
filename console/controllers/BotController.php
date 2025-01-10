<?php

namespace console\controllers;

use api\resources\SemestrUpdate;
use common\models\model\AttendReason;
use common\models\model\EduPlan;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrExamsType;
use common\models\model\EduSemestrSubject;
use common\models\model\FinalExam;
use common\models\model\FinalExamGroup;
use common\models\model\PasswordEncrypts;
use common\models\model\Student;
use common\models\model\StudentAttend;
use common\models\model\StudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentMarkHistory;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use common\models\model\StudentTopicPermission;
use common\models\model\SubjectVedomst;
use common\models\model\Timetable;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\model\TimetableReason;
use common\models\model\TimetableStudent;
use common\models\Profile;
use common\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;

class BotController extends Controller
{
    public function actionBot()
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