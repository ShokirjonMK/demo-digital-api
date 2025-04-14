<?php

namespace common\models\model;

use Yii;

class TeacherAccessStatistic1 extends TeacherAccess
{

    public function fields()
    {
        $fields =  [
            'id',
            'examName' => function ($model) {
                return $model->examSemeta->exam->translate->name ?? '';
            },
            'chalaCount' => function ($model) {
                return $model->chalaCount ?? 0;
            },


            // 'subject' => function ($model) {
            //     return $model->subject->name ?? '';
            // },

            // 'examStudentCount' => function ($model) {
            //     return $model->examStudentCount ?? 0;
            // },

            // 'checkedCount' => function ($model) {
            //     return $model->checkCount ?? 0;
            // },
            // 'mustCheckCount' => function ($model) {
            //     return $model->mustCheckCount ?? 0;
            // },

            // 'actCount' => function ($model) {
            //     return $model->actCount ?? 0;
            // },
            // 'hasAnswerCount' => function ($model) {
            //     return $model->notCount ?? 0;
            // },


        ];

        return $fields;
    }


    public function extraFields()
    {
        $extraFields =  [
            'languages',
            'subject',
            'teacher',
            'user',

            'examStudentCount',
            'examStudent',
            'checkedCount',
            'checkCount',
            'mustCheckCount',


            'actCount',
            'notCount',


            'timeTables',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getChalaCount()
    {
        $model = new ExamStudentAnswerSubQuestion();

        $query = $model->find();
        $query->andWhere([
            'in', 'exam_student_answer_id', ExamStudentAnswer::find()->select('id')
                ->where([
                    'in', 'exam_student_id',
                    ExamStudent::find()->select('id')->where(['teacher_access_id' => $this->id])->andWhere(['!=', 'act', 1])
                ])
        ]);

        // $query->leftJoin('exam_student_answer_sub_question', 'exam_student_answer_sub_question.exam_student_answer_id = ' . $model->tableName() . '.id');
        // $query->andWhere(['>=', 'esa.ball', 0]);
        $query->andWhere([
            'or',
            ['ball' => null],
            ['teacher_conclusion' => null]
        ]);

        // dd($query->createCommand()->rawSql());
        $query->all();

        return $query->count();
    }

    public function getCheckCount()
    {
        $model = new ExamStudent();
        $query = $model->find();
        $query = $query->andWhere([$model->tableName() . '.teacher_access_id' => $this->id])
            ->leftJoin("exam_student_answer", "exam_student_answer.exam_student_id = " . $model->tableName() . ".id ")
            ->leftJoin("exam_student_answer_sub_question", "exam_student_answer_sub_question.exam_student_answer_id = exam_student_answer.id")
            // ->andWhere(['not', ['esasq.ball' => null, 'esasq.teacher_conclusion' => null]])

            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.ball', null])
            ->andWhere(['IS NOT', 'exam_student_answer_sub_question.teacher_conclusion', null])
            ->groupBy('exam_student.id');

        // dd($query->createCommand()->getRawSql());
        // dd("qweqwe");
        // return 122;
        return count($query->all());
    }

    public function getMustCheckCount()
    {
        $model = new ExamStudent();
        $query = $model->find();
        $query = $query->andWhere([$model->tableName() . '.teacher_access_id' => $this->id])
            ->leftJoin("exam_student_answer", "exam_student_answer.exam_student_id = " . $model->tableName() . ".id ")
            ->leftJoin("exam_student_answer_sub_question", "exam_student_answer_sub_question.exam_student_answer_id = exam_student_answer.id")
            // ->andWhere(['not', ['esasq.ball' => null, 'esasq.teacher_conclusion' => null]])
        ;
        $query->andWhere([
            'or',
            ['exam_student_answer_sub_question.ball' => null],
            ['exam_student_answer_sub_question.teacher_conclusion' => null]
        ])
            ->groupBy('exam_student.id');

        // dd($query->createCommand()->getRawSql());
        // dd("qweqwe");
        // return 122;
        return count($query->all());
    }


    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }

    public function getExamStudentAct()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id'])->onCondition(['act' => 1]);
    }

    public function getExamStudentNot()
    {
        $model = new ExamStudentAnswer();
        $query = $model->find();

        return $query->andWhere([
            'in', 'exam_student_id',
            ExamStudent::find()
                ->select('id')
                ->where(['teacher_access_id' => $this->id])
        ])->all();
    }

    public function getNotCount()
    {
        return count($this->examStudentNot);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    public function getActCount()
    {
        return count($this->examStudentAct);
    }

    public function getExamSemeta()
    {
        return $this->hasOne(ExamSemeta::className(), ['teacher_access_id' => 'id']);
    }

    public function getExams()
    {
        return $this->examStudentOne->exam;
    }

    public function getExamStudentOne()
    {
        return $this->hasOne(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }


    public function getCheckedCount()
    {
        $model = new ExamStudentAnswer();

        $query = $model->find();
        $query->andWhere([
            'in', 'exam_student_id',
            ExamStudent::find()->select('id')->where(['teacher_access_id' => $this->id])
        ]);

        $query->leftJoin('exam_student_answer_sub_question', 'exam_student_answer_sub_question.exam_student_answer_id = ' . $model->tableName() . '.id');
        // $query->andWhere(['>=', 'esa.ball', 0]);
        $query->andWhere([
            'and',
            ['!=', 'exam_student_answer_sub_question.ball', null],
            ['!=', 'exam_student_answer_sub_question.teacher_conclusion', null]
        ]);

        // dd($query->createCommand()->rawSql());
        $query->all();

        return $query->count();
    }
}
