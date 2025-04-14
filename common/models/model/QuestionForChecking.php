<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;



class QuestionForChecking extends Question
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }


    public function fields()
    {
        $fields = [
            'id',

            'course_id',
            'semestr_id',
            'subject_id',
            'subQuestion' => function ($model) {
                return $model->subQuestions ?? [];
            },
            'question_file' => function ($model) {
                return $model->file ?? '';
            },
            'options' => function ($model) {
                return $model->options ?? [];
            },
            'file',
            // 'ball',
            'question',
            'lang_id',
            'level',
            'question_type_id',
            'description',
            // 'order',
            'status',
            // 'created_at',
            // 'updated_at',
            // 'created_by',
            // 'updated_by',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields = [
            'course',
            'semestr',
            'options',
            'subject',
            'lang',
            'questionType',
            'subQuestions',

            'statusName',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
}
