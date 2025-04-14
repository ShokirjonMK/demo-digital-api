<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class ExamForSubject extends Exam
{
    public static $selected_language = 'uz';

    use ResourceTrait;




    public function fields()
    {
        $fields =  [
            // 'id',
            // 'name' => function ($model) {
            //     return $model->translate->name ?? '';
            // },
            'examStudentByLang' => function ($model) {
                return $model->examStudentByLang ?? [];
            },


        ];
        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'examStudentByLang',
        ];

        return $extraFields;
    }



    public function getExamStudentByLang()
    {
        return (new yii\db\Query())
            ->from('exam_student')
            ->select(['COUNT(*) AS count', 'lang_id'])
            ->where(['exam_id' => $this->id])
            ->groupBy(['lang_id'])
            ->all();
    }
}
