<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

class ExamStudentAnswerForTeacher extends ExamStudentAnswer
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',

            'teacher_conclusion' => 'Еeacher Сonclusion',
            'ball' => 'Ball',

            'status' => _e('Status'),

            'updated_at' => _e('Updated At'),

            'updated_by' => _e('Updated By'),

        ];
    }


    public function fields()
    {
        $fields = [
            'id',
            'parent_id',
            'file',
            'exam_id',

            // 'question' => function ($model) {
            //     return $model->questionForExamStudentAnswer ?? [];
            // },
            'question_type' => function ($model) {
                return $model->questionType->name ?? '';
            },

            'question_id',
            'exam_student_id',
            'teacher_conclusion',
            'appeal_teacher_conclusion',
            'option_id',
            'answer',
            'ball',
            'max_ball',
            'teacher_access_id',
            'attempt',
            'type',
            'order',
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
            'exam',
            'examStudent',
            'question',
            'questionOnly',
            'option',
            'teacherAccess',
            'questionType',
            'questionForExamStudentAnswer',
            //            'subQuestionAnswers',
            'examStudentAnswerSubQuestion',
            // 'subQuestionAnswers',
            'statusName',

            'subQuestions',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
}
