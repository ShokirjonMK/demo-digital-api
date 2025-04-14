<?php

namespace api\controllers;

use common\models\model\ExamAppeal;
use Yii;
use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\Student;
use yii\caching\DbDependency;
use yii\db\Query;

class ExamAppealController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamAppeal';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_appeal';
    public $controller_name = 'ExamAppeal';

    public function actionIndex($lang)
    {
        $model = new ExamAppeal();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andWhere([$this->table_name . '.archived' => 0])
            ->andFilterWhere(['like', $this->table_name . 'appeal_text', Yii::$app->request->get('query')]);


        $query->join('INNER JOIN', 'student', 'student.id = exam_appeal.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->andFilterWhere(['like', 'option', Yii::$app->request->get('query')]);


        //  Filter from Profile 
        if (isRole('admin')) {
            $profile = new Profile();
            if (isset($filter)) {
                foreach ($filter as $attribute => $id) {
                    if (in_array($attribute, $profile->attributes())) {
                        $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
                    }
                }
            }

            $queryfilter = Yii::$app->request->get('filter-like');
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if (isset($queryfilter)) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $profile->attributes())) {
                        $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }
        // ***

        /**   filtering ball difference between old_ball ball
            'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
            'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
            'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
            'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
            'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
            'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
            'total_appeals' => 'COUNT(*)'
         */

        // $ball_diff = Yii::$app->request->get('ball_diff');
        // if (isset($queryfilter)) {
        //     if ($ball_diff == 'diff_less_than_5') {
        //         // specific filter diff_less_than_5 
        //         // $query->andWhere(['>', new \yii\db\Expression('ABS(exam_appeal.old_ball - exam_appeal.ball)'), 0])
        //         //     ->andWhere(['<=', new \yii\db\Expression('ABS(exam_appeal.old_ball - exam_appeal.ball)'), 5]);
        //         $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5');
        //     }
        //     if ($ball_diff == 'diff_6_to_10') {

        //         // specific filter diff_6_to_10 
        //         $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10');
        //     }
        //     if ($ball_diff == 'diff_11_to_20') {
        //         // specific filter diff_11_to_20 
        //         $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20');
        //     }
        //     if ($ball_diff == 'diff_21_to_40') {
        //         // specific filter diff_21_to_40 
        //         $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40');
        //     }
        //     if ($ball_diff == 'diff_41_to_60') {
        //         // specific filter diff_41_to_60 
        //         $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60');
        //     }
        // }


        // $ball_diff = Yii::$app->request->get('ball_diff');

        // Check if the 'queryfilter' variable is set. If it is, proceed with further filtering based on 'ball_diff'.
        // if (isset($queryfilter)) {

        //     // Use a switch statement to handle multiple conditions based on the value of 'ball_diff'.
        //     switch ($ball_diff) {
        //         case 'diff_less_than_5':
        //             // Filter for cases where the absolute difference between old_ball and ball is less than or equal to 5 and greater than 0.
        //             $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5');
        //             break;

        //         case 'diff_6_to_10':
        //             // Filter for cases where the absolute difference between old_ball and ball is between 6 and 10.
        //             $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10');
        //             break;

        //         case 'diff_11_to_20':
        //             // Filter for cases where the absolute difference between old_ball and ball is between 11 and 20.
        //             $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20');
        //             break;

        //         case 'diff_21_to_40':
        //             // Filter for cases where the absolute difference between old_ball and ball is between 21 and 40.
        //             $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40');
        //             break;

        //         case 'diff_41_to_60':
        //             // Filter for cases where the absolute difference between old_ball and ball is between 41 and 60.
        //             $query->andWhere('ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60');
        //             break;

        //         default:
        //             // Optionally, handle other cases or do nothing.
        //             break;
        //     }
        // }

        $ball_diff = Yii::$app->request->get('ball_diff');

        // Define an associative array mapping ball_diff keys to SQL conditions
        $conditionMap = [
            'diff_less_than_5' => 'ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5',
            'diff_6_to_10'     => 'ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10',
            'diff_11_to_20'    => 'ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20',
            'diff_21_to_40'    => 'ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40',
            'diff_41_to_60'    => 'ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60',
        ];

        // Check if queryfilter is set. If yes, proceed with further filtering.
        if (isset($ball_diff)) {
            // Check if the current ball_diff key exists in the condition map.
            if (isset($conditionMap[$ball_diff])) {
                // Add the SQL snippet to the query's WHERE clause.
                $query->andWhere($conditionMap[$ball_diff]);
            }
        }



        if (isRole("teacher")) {
            $query = $query->andWhere([
                'in',
                $this->table_name . '.teacher_access_id',
                $this->teacher_access()
            ]);
        }

        if (isRole("student")) {
            $query = $query->andWhere([$this->table_name . '.created_by' => current_user_id()]);
        }

        $query->orderBy(['id' => SORT_DESC]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data caching
        // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM exam_appeal']));

        // data
        // rawsql($query);
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new ExamAppeal();

        // $errors['time'] = time();
        // // $errors['appeal_finish'] = $model->examStudent->exam->appeal_finish;

        // return simplify_errors($errors);

        if (!isRole('student') && !isRole('admin')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        // $student = Student::findOne(['user_id' => current_user_id()]);
        // if (!$student) {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, _e('Student not found'), ResponseStatus::UPROCESSABLE_ENTITY);
        // }

        $post = Yii::$app->request->post();


        $this->load($model, $post);
        $result = ExamAppeal::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    /**
     * Retrieve statistical data about the difference in scores after exam appeals.
     * 
     * The results can be grouped by:
     * - faculty
     * - subject
     * - kafedra
     * 
     * @param string $lang The language for translations.
     * @return Response A formatted response containing data or errors.
     */
    public function actionBall($lang)
    {
        $request = Yii::$app->request;

        // Base Query: Filtering appeals that are not archived and computing various differences.
        $baseQuery = (new \yii\db\Query())
            ->select([
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

            // ->where(['exam.archived' => 1])
            ->where(['exam_appeal.archived' => 0]);

        // Checking which grouping to apply based on the request parameters.
        if (null !== $request->get('faculty')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'exam_appeal.faculty_id',
                'tr.name AS faculty_name'
            ])
                ->join('JOIN', 'translate tr', 'exam_appeal.faculty_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'faculty\'', [':lang' => $lang])
                ->groupBy('exam_appeal.faculty_id');
        } elseif (null !== $request->get('subject')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'exam_appeal.subject_id',
                'tr.name AS subject_name'
            ])
                ->join('JOIN', 'translate tr', 'exam_appeal.subject_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'subject\'', [':lang' => $lang])
                ->groupBy('exam_appeal.subject_id');
        } elseif (null !== $request->get('kafedra')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'subject.kafedra_id',
                'tr.name AS kafedra_name'  // Changed to 'kafedra_name' for clarity.
            ])
                ->join('JOIN', 'subject', 'subject.id = exam_appeal.subject_id')
                ->join('JOIN', 'translate tr', 'subject.kafedra_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'kafedra\'', [':lang' => $lang]) // Adjusted join to 'kafedra' for clarity.
                ->groupBy('subject.kafedra_id');
        } elseif (null !== $request->get('teacher')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'exam_appeal.teacher_user_id',
                // 'subject.kafedra_id',

                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                // 'tr.name AS kafedra_name'  // Changed to 'kafedra_name' for clarity.
            ])
                ->join('JOIN', 'profile', 'profile.user_id = exam_appeal.teacher_user_id')
                // ->join('JOIN', 'translate tr', 'subject.kafedra_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'kafedra\'', [':lang' => $lang]) // Adjusted join to 'kafedra' for clarity.
                ->groupBy([
                    // 'subject.kafedra_id',

                    'exam_appeal.teacher_user_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    // 'tr.name'
                ]);
        } else {
            // If no specific group parameter is provided, return a success message with no data.
            return $this->response(1, _e('Success'), null, null, ResponseStatus::OK);
        }

        // Execute the formulated query and retrieve the result.

        // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM exam_appeal']));
        // $query = $query->cache(3600, new DbDependency());

        $result = $query->all();
        return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    }

    // public function actionBall00($lang)
    // {
    //     $request = Yii::$app->request;
    //     $baseQuery = (new \yii\db\Query())
    //         ->select([
    //             'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
    //             'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
    //             'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
    //             'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
    //             'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
    //             'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
    //             'total_appeals' => 'COUNT(*)'
    //         ])
    //         ->from('exam_appeal')->where(['exam_appeal.archived' => 0]);

    //     if (null !== $request->get('faculty')) {
    //         $query = clone $baseQuery;
    //         $query->addSelect([
    //             'faculty_id',
    //             'tr.name AS faculty_name'
    //         ])
    //             ->join('JOIN', 'translate tr', 'exam_appeal.faculty_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'faculty\'', [':lang' => $lang])
    //             ->groupBy('faculty_id');
    //     } elseif (null !== $request->get('subject')) {
    //         $query = clone $baseQuery;
    //         $query->addSelect([
    //             'exam_appeal.subject_id',
    //             'tr.name AS subject_name'
    //         ])
    //             ->join('JOIN', 'translate tr', 'exam_appeal.subject_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'subject\'', [':lang' => $lang])
    //             ->groupBy('exam_appeal.subject_id');
    //     } elseif (null !== $request->get('kafedra')) {
    //         $query = clone $baseQuery;
    //         $query->addSelect([
    //             'subject.kafedra_id',
    //             'tr.name AS subject_name'
    //         ])
    //             // ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
    //             ->join('JOIN', 'subject', 'exam.subject_id = exam_appeal.subject_id')
    //             ->join('JOIN', 'translate tr', 'exam_appeal.subject_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'subject\'', [':lang' => $lang])
    //             ->groupBy('subject.kafedra_id');
    //     } else {
    //         // If no specific group parameter is provided, return a success message with no data.
    //         return $this->response(1, _e('Success'), null, null, ResponseStatus::OK);
    //     }

    //     $result = $query->all();
    //     return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    // }

    public function actionKafedra($lang)
    {
        $request = Yii::$app->request;
        $baseQuery = (new \yii\db\Query())
            ->select([
                'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
                'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
                'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
                'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
                'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
                'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
                'total_appeals' => 'COUNT(*)'
            ])
            ->from('exam_appeal')->where(['exam_appeal.archived' => 0]);

        if (null !== $request->get('faculty')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'faculty_id',
                'tr.name AS faculty_name'
            ])
                ->join('JOIN', 'translate tr', 'exam_appeal.faculty_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'faculty\'', [':lang' => $lang])
                ->groupBy('faculty_id');
        } elseif (null !== $request->get('subject')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'exam_appeal.subject_id',
                'tr.name AS subject_name'
            ])
                ->join('JOIN', 'translate tr', 'exam_appeal.subject_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'subject\'', [':lang' => $lang])
                ->groupBy('exam_appeal.subject_id');
        } elseif (null !== $request->get('kafedra')) {
            $query = clone $baseQuery;
            $query->addSelect([
                'exam.subject_id',
                'tr.name AS subject_name'
            ])
                ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
                ->join('JOIN', 'translate tr', 'exam.subject_id = tr.model_id AND tr.`language`=:lang AND tr.table_name =\'subject\'', [':lang' => $lang])
                ->groupBy('exam.subject_id');
        } else {
            // If no specific group parameter is provided, return a success message with no data.
            return $this->response(1, _e('Success'), null, null, ResponseStatus::OK);
        }

        $result = $query->all();
        return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    }


    // public function actionBall3($lang)
    // {
    //     $type = null;
    //     $table = null;

    //     if (null !==  Yii::$app->request->get('faculty')) {
    //         $type = 'faculty';
    //         $table = 'faculty';
    //     } elseif (null !==  Yii::$app->request->get('subject')) {
    //         $type = 'subject';
    //         $table = 'subject';
    //     } elseif (null !==  Yii::$app->request->get('kafedra')) {
    //         $type = 'kafedra';
    //         $table = 'subject';
    //     }

    //     if ($type !== null) {
    //         $query = $this->createQuery($type, $table);
    //         $result = $query->all();
    //         return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    //     }

    //     return $this->response(1, _e('Success'), null, null, ResponseStatus::OK);
    // }

    // private function createQuery($type, $table)
    // {
    //     $query = (new \yii\db\Query())
    //         ->select([
    //             // ... (Your SELECT params)
    //         ])
    //         ->from('exam_appeal')
    //         ->join('JOIN', 'translate tr', "exam_appeal.{$type}_id = tr.model_id AND tr.`language`='uz' and tr.table_name ='{$table}'")
    //         ->groupBy("exam_appeal.{$type}_id");

    //     return $query;
    // }

    // public function actionBall321($lang)
    // {
    //     if (null !==  Yii::$app->request->get('faculty')) {

    //         $query = (new \yii\db\Query())
    //             ->select([
    //                 'faculty_id',
    //                 'tr.name AS faculty_name',
    //                 'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
    //                 'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
    //                 'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
    //                 'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
    //                 'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
    //                 'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
    //                 'total_appeals' => 'COUNT(*)'
    //             ])
    //             ->from('exam_appeal')
    //             ->join('JOIN', 'translate tr', 'exam_appeal.faculty_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'faculty\'')
    //             ->groupBy('faculty_id');

    //         $result = $query->all();

    //         return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    //     }

    //     if (null !==  Yii::$app->request->get('subject')) {
    //         $query = (new Query())
    //             ->select([
    //                 'exam_appeal.subject_id',
    //                 'tr.name AS subject_name',
    //                 'no_change' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) = 0 THEN 1 END)',
    //                 'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
    //                 'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
    //                 'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
    //                 'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
    //                 'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
    //                 'total_appeals' => 'COUNT(*)'
    //             ])
    //             ->from('exam_appeal')
    //             ->join('JOIN', 'translate tr', 'exam_appeal.subject_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'subject\'')
    //             ->groupBy('exam_appeal.subject_id');

    //         $result = $query->all();

    //         return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    //     }

    //     if (null !==  Yii::$app->request->get('kafedra')) {
    //         $query = (new Query())
    //             ->select([
    //                 'exam.subject_id',
    //                 'tr.name AS subject_name',
    //                 'diff_less_than_5' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 0 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 5 THEN 1 END)',
    //                 'diff_6_to_10' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 5 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 10 THEN 1 END)',
    //                 'diff_11_to_20' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 10 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 20 THEN 1 END)',
    //                 'diff_21_to_40' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 20 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 40 THEN 1 END)',
    //                 'diff_41_to_60' => 'COUNT(CASE WHEN ABS(exam_appeal.old_ball - exam_appeal.ball) > 40 AND ABS(exam_appeal.old_ball - exam_appeal.ball) <= 60 THEN 1 END)',
    //                 'total_appeals' => 'COUNT(*)'
    //             ])
    //             ->from('exam_appeal')
    //             ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
    //             ->join('JOIN', 'translate tr', 'exam.subject_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'subject\'')
    //             ->groupBy('exam.subject_id');

    //         $result = $query->all();

    //         return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    //     }

    //     return $this->response(1, _e('Success'), null, null, ResponseStatus::OK);
    // }

    public function actionUpdate($lang, $id)
    {
        $model = ExamAppeal::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();


        if (isRole('teacher')) {
            if ($model->teacherAccess->user_id == current_user_id()) {
                $result = ExamAppeal::teacherUpdateItem($model, $post);
                if (!is_array($result)) {
                    return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            }
            return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        }

        // $data = [];

        // if (isRole('teacher')) {

        //     if ($model->teacherAccess->user_id !=  current_user_id()) {
        //         return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        //     }

        //     $examStudentAnswer
        //     if (isset($post['teacher_conclusion'])) {
        //         $data['teacher_conclusion'] = $post['teacher_conclusion'];
        //     }


        //     if ($model->examStudent->teacherAccess->user_id != current_user_id()) {
        //         return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
        //     } else {
        //         $post['teacher_access_id'] = $model->examStudent->teacher_access_id;
        //     }
        //     $data = [];
        //     if (isset($post['teacher_conclusion'])) {
        //         $data['teacher_conclusion'] = $post['teacher_conclusion'];
        //     }
        //     if (isset($post['ball'])) {
        //         $data['ball'] = $post['ball'];
        //     }
        //     if (isset($post['subQuestionAnswersChecking'])) {
        //         $data['subQuestionAnswersChecking'] = $post['subQuestionAnswersChecking'];
        //     }

        //     $this->load($model, $data);
        //     $result = ExamStudentAnswer::updateItemTeacher($model, $data);

        //     if (!is_array($result)) {
        //         return $this->response(1, _e($this->controller_name . ' successfully saved.'), $model, null, ResponseStatus::OK);
        //     } else {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        //     }
        // }


        // sassdlasl;dkasl;d
        /* if (!isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }

        $student = Student::findOne(['user_id' => current_user_id()]);
        if (!$student) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, _e('Student not found'), ResponseStatus::UPROCESSABLE_ENTITY);
        } */


        // $this->load($model, $post);

        $this->load($model, $post);
        $result = ExamAppeal::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamAppeal::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        // dd([(in_array($model->teacher_access_id, array_column($this->teacher_access(2), 'id'))), $model->teacher_access_id, $this->teacher_access(2)]);
        // dd($this->teacher_access(2));
        if (!isRole('admin')) {
            if (!in_array($model->teacher_access_id, array_column($this->teacher_access(2), 'id'))) {
                return $this->response(0, _e('This is not your appeal.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamAppeal::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // $model->is_deleted = 1;
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
