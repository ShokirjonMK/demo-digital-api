<?php

namespace console\controllers;

use common\models\model\ExamAppeal;
use common\models\model\ExamStudent;
use common\models\model\Faculty;
use common\models\Student;
use common\models\model\Statistic;
use yii\console\Controller;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class StudentStatsController extends Controller
{
    public function actionDailyStats()
    {
        try {
            // Disable behaviors for console
            // Statistic::$enableBehaviors = false;

            $date = date('Y-m-d');
            $stats = [];
            $data = Student::find()
                ->select([
                    'edu_form_id', // Ta'lim turi
                    'COUNT(*) AS all_students', // Barcha talabalar soni
                    'SUM(student.gender = 1) AS gender1', // O'g'il bolalar
                    'SUM(student.gender = 0) AS gender0', // Qiz bolalar
                    'SUM(student.edu_lang_id = 1) AS uzbek', // O'zbek
                    'SUM(student.edu_lang_id = 2) AS rus', // Rus
                    'SUM(student.course_id = 1) AS course_id1', // Kurs 1
                    'SUM(student.course_id = 2) AS course_id2', // Kurs 2
                    'SUM(student.course_id = 3) AS course_id3', // Kurs 3
                    'SUM(student.course_id = 4) AS course_id4', // Kurs 4
                    'SUM(student.is_contract = 1) AS contract', // Shartnoma
                    'SUM(student.is_contract = 0) AS grant', // Grant
                    'SUM(profile.has_disability = 1) AS disability', // Grant
                    'SUM(profile.underprivileged = 1) AS underprivileged', // kam ta'minlangan
                    'SUM(profile.house_of_kindness = 1) AS house_of_kindness', // Mehribonlik uyi
                ])
                ->leftJoin('profile', 'student.user_id = profile.user_id')
                ->where(['student.is_deleted' => 0])
                ->where(['student.status' => 10])
                ->groupBy(['student.edu_form_id'])
                ->asArray()
                ->all();

            // Save statistics to database
            $this->saveStats([
                'date' => $date,
                'stats' => $data
            ], 'contingent');

            $lang = 'uz';

            $facultyQuery = ExamStudent::find()
                ->select([
                    'faculty_id',
                    'faculty_name' => 'tr.name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)',
                ])
                ->from('exam_student')
                ->innerJoin('exam', 'exam_student.exam_id = exam.id')
                ->innerJoin('translate tr', 'exam.faculty_id = tr.model_id AND tr.language = :lang AND tr.table_name = \'faculty\'', [':lang' => $lang])
                ->where(['exam.archived' => 0])
                ->groupBy(['faculty_id', 'faculty_name'])
                ->asArray()
                ->all(); // Add tr.name to the GROUP BY clause

            // Save statistics to database
            $this->saveStats([
                'date' => $date,
                'stats' => $facultyQuery
            ], 'faculty_ball');


            $appealQuery = ExamAppeal::find()
                ->select([
                    'exam_appeal.faculty_id',
                    'tr.name AS faculty_name',
                    'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
                    'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
                    'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
                    'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
                    'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
                    'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
                    'total_appeals' => 'COUNT(*)'
                ])
                ->from('exam_appeal')
                ->join('JOIN', 'exam', 'exam.id = exam_appeal.exam_id')
                ->join('JOIN', 'translate tr', 'exam_appeal.faculty_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'faculty\'', [':lang' => $lang])
                // ->where(['exam.archived' => 1])
                ->where(['exam_appeal.archived' => 0])
                ->groupBy('exam_appeal.faculty_id')
                ->asArray()
                ->all();

            // Save statistics to database
            $this->saveStats([
                'date' => $date,
                'stats' => $appealQuery
            ], 'faculty_appeal');



            // // Save statistics to database
            // $this->saveStats([
            //     'date' => $date,
            //     'stats' => $appealQuery
            // ], 'faculty_appeal');





            // // Student congingent statistics by Faculty
            // $dataFacylty = [];
            // $facultyes = Faculty::find()->andWhere(['is_deleted' => 0, 'status' => 1])->all();
            // foreach ($facultyes as $faculty) {
            //     $dataFacylty[$faculty->id] = Student::find()
            //         ->select([
            //             'edu_form_id', // Ta'lim turi
            //             'COUNT(*) AS all_students', // Barcha talabalar soni
            //             'SUM(gender = 1) AS gender1', // O'g'il bolalar
            //             'SUM(gender = 0) AS gender0', // Qiz bolalar
            //             'SUM(edu_lang_id = 1) AS uzbek', // O'zbek
            //             'SUM(edu_lang_id = 2) AS rus', // Rus
            //             'SUM(course_id = 1) AS course_id1', // Kurs 1
            //             'SUM(course_id = 2) AS course_id2', // Kurs 2
            //             'SUM(course_id = 3) AS course_id3', // Kurs 3
            //             'SUM(course_id = 4) AS course_id4', // Kurs 4
            //             'SUM(is_contract = 1) AS contract', // Shartnoma
            //             'SUM(is_contract = 0) AS grant', // Grant
            //         ])
            //         ->where(['is_deleted' => 0])
            //         ->where(['faculty_id' => $faculty->id, 'status' => 10])
            //         ->groupBy(['edu_form_id'])
            //         ->asArray()
            //         ->all();

            //     // Save statistics to database

            // }
            // $this->saveStats([
            //     'date' => $date,
            //     'stats' => $dataFacylty
            // ], 'contingent_faculty');
            echo "Daily statistics generated successfully for {$date}\n";
        } catch (\Exception $e) {
            echo "Error generating statistics: " . $e->getMessage() . "\n";
            \Yii::error("Error in daily student statistics: " . $e->getMessage());
        }
    }

    private function saveStats($data, $key = 'contingent')
    {
        // Check if statistics for this date already exist
        // $key = 'contingent';
        $model = Statistic::findOne(['key' => $key, 'date' => $data['date']]);

        if (!$model) {
            $model = new Statistic();
            $model->key = $key;
            $model->type = 1;
            $model->status = 1;
            $model->is_deleted = 0;
            $model->archived = 0;
            $model->created_at = time();
            $model->created_by = 1;
        }

        $model->data = $data['stats'];
        $model->date = $data['date'];
        $model->updated_at = time();
        $model->updated_by = 1;

        if ($model->save(false)) { // Skip validation since we're in console
            echo "Statistics saved to database successfully.\n";
        } else {
            echo "Error saving statistics: " . json_encode($model->errors) . "\n";
        }
    }
}
