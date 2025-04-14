<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\model\ActionLog;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubject;
use common\models\model\EduYear;
use common\models\model\ExamControlStudent;
use common\models\model\ExamStudent;
use common\models\model\Student;
use common\models\model\StudentMark;
use Yii;
use yii\helpers\Console;

class StudentMarkController extends Controller
{
    public function actionStart($edu_year_id = null)
    {
        $this->stdout("\n\n Student Mark writing start ...\n\n", Console::FG_GREEN);
        if (isNull($edu_year_id)) {
            $edu_year_id = EduYear::findOne(['status' => 1, 'is_deleted' => 0])->id;
        }
        $this->stdout("\n\n Edu Year ID is $edu_year_id \n\n", Console::FG_GREEN);


        $new = new StudentMark();
        // [['student_id', 'subject_id', 'edu_semestr_id', 'edu_semestr_subject_id'], 'required'],

        $eduSemestrs = EduSemestr::find()
            ->where(['edu_year_id' => $edu_year_id])
            ->all();

        foreach ($eduSemestrs as $eduSemestr) {

            $students = Student::find()
                ->where(['edu_plan_id' => $eduSemestr->edu_plan_id])
                ->andWhere(['status' => 10, 'is_deleted' => 0])
                ->all();

            $eduSemestrSubjects = EduSemestrSubject::find()
                ->where(['edu_semestr_id' => $eduSemestr->id])
                ->all();

            foreach ($eduSemestrSubjects as $eduSemestrSubject) {
                foreach ($students as $student) {
                    $new = new StudentMark();

                    $new->attempt = StudentMark::find()->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id,])->count() + 1;

                    $new->student_id = $student->id;
                    $new->subject_id = $eduSemestrSubject->subject_id;
                    $new->edu_semestr_id = $eduSemestr->id;
                    $new->edu_semestr_subject_id = $eduSemestrSubject->id;
                    $new->course_id = $eduSemestr->course_id;
                    $new->semestr_id = $eduSemestr->semestr_id;
                    $new->edu_year_id = $eduSemestr->edu_year_id;
                    $new->faculty_id = $student->faculty_id;
                    $new->edu_plan_id = $student->edu_plan_id;
                    $exam_control_student = ExamControlStudent::find()->where([
                        'student_id' => $student->id,
                        'subject_id' => $eduSemestrSubject->subject_id,
                        // 'semester_id' => $eduSemester->id,
                    ])->orderBy(['id' => 'DESC'])->one();
                    if ($exam_control_student) {

                        $new->exam_control_student_ball = $exam_control_student->ball;
                        $new->exam_control_student_ball2 = $exam_control_student->ball2;
                    }

                    $exam_student = ExamStudent::find()->where([
                        'student_id' => $student->id,
                        'subject_id' => $eduSemestrSubject->subject_id,
                        // 'semester_id' => $eduSemester->id,
                    ])->orderBy(['id' => 'DESC'])->one();

                    if ($exam_student) {
                        $new->exam_student_ball = $exam_student->ball;
                    }

                    // $new->attempt = 1;
                    $new->edu_lang_id = $student->edu_lang_id;

                    if (!$new->save()) {
                        $errors[] = $new->error;
                    }
                }
            }
        }
        if (count($errors) > 0) {
            $this->stdout("\n\n Student Mark writing is finished ...\n\n", Console::FG_GREEN);
        }

        $this->stdout($errors, Console::FG_RED);
    }

    public function actionGo($edu_year_id = null)
    {
        $this->stdout("\n\n Student Mark writing start ...\n\n", Console::FG_GREEN);

        if ($edu_year_id === null) {
            $edu_year_id = EduYear::find()->where(['status' => 1, 'is_deleted' => 0])->one()->id;
        }

        $this->stdout("\n\n Edu Year ID is $edu_year_id \n\n", Console::FG_GREEN);

        $errors = [];

        $eduSemestrs = EduSemestr::find()->where(['edu_year_id' => $edu_year_id])->all();

        foreach ($eduSemestrs as $eduSemestr) {
            $students = Student::find()
                ->where(['edu_plan_id' => $eduSemestr->edu_plan_id, 'status' => 10, 'is_deleted' => 0])
                ->all();

            $eduSemestrSubjects = EduSemestrSubject::find()->where(['edu_semestr_id' => $eduSemestr->id])->all();

            foreach ($eduSemestrSubjects as $eduSemestrSubject) {
                foreach ($students as $student) {
                    $new = new StudentMark();

                    $new->attempt = StudentMark::find()->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id])->count() + 1;

                    $new->attributes = [
                        'student_id' => $student->id,
                        'subject_id' => $eduSemestrSubject->subject_id,
                        'edu_semestr_id' => $eduSemestr->id,
                        'edu_semestr_subject_id' => $eduSemestrSubject->id,
                        'course_id' => $eduSemestr->course_id,
                        'semestr_id' => $eduSemestr->semestr_id,
                        'edu_year_id' => $eduSemestr->edu_year_id,
                        'faculty_id' => $student->faculty_id,
                        'edu_plan_id' => $student->edu_plan_id,
                        'edu_lang_id' => $student->edu_lang_id,
                    ];

                    $examControlStudent = ExamControlStudent::find()
                        ->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id])
                        ->andWhere(['student_mark_id' => null])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($examControlStudent) {
                        $new->exam_control_student_ball = $examControlStudent->ball;
                        $new->exam_control_student_ball2 = $examControlStudent->ball2;
                    }

                    $examStudent = ExamStudent::find()
                        ->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id])
                        ->andWhere(['student_mark_id' => null])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($examStudent) {
                        $new->exam_student_ball = $examStudent->ball;
                    }

                    if (!$new->save()) {
                        $errors[] = $new->errors;
                    }

                    $examStudent->student_mark_id = $new->id;
                    $examStudent->save(false);
                    $examControlStudent->student_mark_id = $new->id;
                    $examControlStudent->save(false);
                }
            }
        }

        if (count($errors) > 0) {
            $this->stdout("\n\n Student Mark writing is finished ...\n\n", Console::FG_RED);
        }
        $this->stdout($errors, Console::FG_RED);
    }

    public function actionGoon($edu_year_id = null)
    {
        $this->stdout("\n\n Student Mark writing start ...\n\n", Console::FG_GREEN);
        // $this->stdout($edu_year_id, Console::FG_GREEN);

        // die();

        if ($edu_year_id === null) {
            $edu_year_id = EduYear::find()->where(['status' => 1, 'is_deleted' => 0])->one()->id;
        }

        $this->stdout("\n Edu Year ID is $edu_year_id \n\n", Console::FG_GREEN);

        $errors = [];

        $eduSemestrs = EduSemestr::find()->where(['edu_year_id' => $edu_year_id])->all();
        foreach ($eduSemestrs as $eduSemestr) {
            $students = Student::find()
                ->where(['edu_plan_id' => $eduSemestr->edu_plan_id, 'status' => 10, 'is_deleted' => 0])
                ->all();

            $eduSemestrSubjects = EduSemestrSubject::find()->where(['edu_semestr_id' => $eduSemestr->id])->all();

            foreach ($eduSemestrSubjects as $eduSemestrSubject) {
                foreach ($students as $student) {
                    $new = new StudentMark();

                    $new->attempt = StudentMark::find()->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id])->count() + 1;

                    $new->attributes = [
                        'student_id' => $student->id,
                        'subject_id' => $eduSemestrSubject->subject_id,
                        'edu_semestr_id' => $eduSemestr->id,
                        'edu_semestr_subject_id' => $eduSemestrSubject->id,
                        'course_id' => $eduSemestr->course_id,
                        'semestr_id' => $eduSemestr->semestr_id,
                        'edu_year_id' => $eduSemestr->edu_year_id,
                        'faculty_id' => $student->faculty_id,
                        'edu_plan_id' => $student->edu_plan_id,
                        'edu_lang_id' => $student->edu_lang_id,
                    ];

                    $examControlStudent = ExamControlStudent::find()
                        ->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id, 'student_mark_id' => null])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($examControlStudent) {
                        $new->exam_control_student_ball = $examControlStudent->ball;
                        $new->exam_control_student_ball2 = $examControlStudent->ball2;
                    }

                    $examStudent = ExamStudent::find()
                        ->where(['student_id' => $student->id, 'subject_id' => $eduSemestrSubject->subject_id, 'student_mark_id' => null])
                        ->orderBy(['id' => SORT_DESC])
                        ->one();

                    if ($examStudent) {
                        $new->exam_student_ball = $examStudent->ball;
                    }
                    $new->save();

                    // dd($new->errors);

                    $this->stdout("new->save() \n\n", Console::FG_RED);

                    if ($new->save()) {

                        $examStudent->student_mark_id = $new->id;
                        $examStudent->save(false);
                        $examControlStudent->student_mark_id = $new->id;
                        $examControlStudent->save(false);
                        dd('shuq');
                    } else {
                        $errors[] = $new->errors;

                        dd('shuaaq');
                    }
                    dd($new);
                }
            }
        }

        if (count($errors) > 0) {
            $this->stdout("\n\n Student Mark writing is finished ...\n\n", Console::FG_RED);
        }

        $this->stdout($errors, Console::FG_RED);
    }



    public function actionCreate()
    {
    }


    public function actionDeleteBase($date)
    {
        $logs = ActionLog::deleteAll(['log_date' => $date]);
    }
}
