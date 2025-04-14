<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\ExamNoStudent;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentReexam;
use common\models\model\Profile;
use common\models\model\Exam;
use common\models\model\ExamStudentAnswer;
use common\models\model\ExamSupervisor;
use yii\caching\DbDependency;
use yii\db\Expression;
use yii\db\Query;

class ExamStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamQuestionOption';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student';
    public $controller_name = 'Exam Student';

    public function actionCorrect($lang, $key)
    {
        // return $key;
        // $rows = (new \yii\db\Query())
        // ->from('user')
        // ->where(['last_name' => 'Smith'])
        // ->limit(10)
        // ->all();

        // $model = new ExamStudent();
        // $i = 0;
        // for ($i = 0; $i <= 4; $i++) {
        // }

        ExamStudent::correct($key);

        return "Success";
    }


    public function actionIndex($lang)
    {
        /** */
        $model = new ExamStudent();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            // ->join('LEFT JOIN', 'exam', 'exam.id = student.user_id')
            ->join('LEFT JOIN', 'exam', 'exam.id = ' . $model->tableName() . '.exam_id')
            ->andFilterWhere(['like', 'option', Yii::$app->request->get('query')]);


        if (Yii::$app->request->get('yes')) {
            $query->andFilterWhere([
                'in',
                $model->tableName() . '.student_id',
                [
                    // 10997, 1453, 1469, 6090, 7677, 9818, 6223, 5030, 8770, 1428, 1981, 1303, 6883, 7593, 5519, 5515
                    // 10997, 1453, 1469, 6090, 7677,9818,6223, 5030, 8770, 1428, 1981, 1303, 6883, 7593, 5519, 5515, 4870, 2434
                    1428,
                    1469,
                    10997,
                    5515,
                    5519,
                    5030,
                    7677,
                    8770,
                    1981,
                    6223,
                    1453,
                    6883,
                    7593,
                    2434,
                    9818,
                    6090,
                    1303,
                    8392

                ]
            ]);
        }

        if (Yii::$app->request->get('in_id')) {
            $query->andFilterWhere([
                'in',
                $model->tableName() . '.id',
                [
                    Yii::$app->request->get('in_id')
                ]
            ]);
        }

        $questionId = Yii::$app->request->get('question_id');
        if ($questionId !== null) {
            $query->innerJoin(
                ExamStudentAnswer::tableName(),
                $this->table_name . '.id = ' . ExamStudentAnswer::tableName() . '.exam_student_id'
            )
                ->andWhere(['question_id' => $questionId]);
        }

        if (Yii::$app->request->get('supervisor')) {
            $query->andFilterWhere([
                'in',
                $this->table_name . '.exam_id',
                ExamSupervisor::find()
                    ->andWhere(['user_id' => current_user_id()])
                    ->andWhere(['is_deleted' => 0])
                    ->select('exam_id')
            ]);

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data caching
            // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM exam']));
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
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
        // ***

        if (isRole("teacher")) {
            $query = $query->andWhere([
                'in',
                $model->tableName() . '.teacher_access_id',
                $this->teacher_access()
            ]);
            // add random order
            $query = $query->orderBy(new Expression('rand()'));
        }

        if (isRole("student")) {
            $query = $query->andWhere([
                $model->tableName() . '.student_id' => $this->student(),
                'exam.status' => Exam::STATUS_ANNOUNCED
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM exam_student']));

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new ExamStudent();
        $post = Yii::$app->request->post();

        if (isset($post['duration'])) {
            $post['duration'] = str_replace("'", "", $post['duration']);
            $post['duration'] = str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['start'])) {
            $post['start'] = strtotime($post['start']);
        }

        if (isset($post['finish'])) {
            $post['finish'] = strtotime($post['finish']);
        }

        $this->load($model, $post);

        $result = ExamStudent::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        if (isRole('teacher')) {
            $model = ExamNoStudent::findOne($id);
        } else {
            $model = ExamStudent::findOne($id);
        }
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole("teacher")) {
            if (is_null($model->teacher_access_id)) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This Exam Student did not given'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
            if ($model->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }

        $post = Yii::$app->request->post();

        if (isset($post['duration'])) {
            $post['duration'] = str_replace("'", "", $post['duration']);
            $post['duration'] = str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['start'])) {
            $post['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $post['finish'] = strtotime($post['finish']);
        }

        // $post['old_file'] = $model->plagiat_file;

        $this->load($model, $post);
        // if (isRole("teacher")) {
        //     $model->status = ExamStudent::STATUS_CHECKED;
        // }
        $result = ExamStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionBall($lang)
    {
        if (null !== Yii::$app->request->get('faculty')) {
            $facultyQuery = (new Query())
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
                ->groupBy(['faculty_id', 'faculty_name']); // Add tr.name to the GROUP BY clause

            $result = $facultyQuery->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('subject')) {
            $subjectQuery = (new Query())
                ->select([
                    'subject_id' => 'subject.id',
                    'subject_name' => 'subject_tr.name',
                    'kafedra_id' => 'subject.kafedra_id',
                    'kafedra_name' => 'kafedra_tr.name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)',
                ])
                ->from('exam_student')
                ->innerJoin('exam', 'exam_student.exam_id = exam.id')
                ->innerJoin('subject', 'subject.id = exam_student.subject_id')
                ->innerJoin('translate subject_tr', 'subject.id = subject_tr.model_id AND subject_tr.language = :lang AND subject_tr.table_name = \'subject\'', [':lang' => $lang])
                ->innerJoin('translate kafedra_tr', 'subject.kafedra_id = kafedra_tr.model_id AND kafedra_tr.language = :lang AND kafedra_tr.table_name = \'kafedra\'', [':lang' => $lang])
                ->where(['exam.archived' => 0])
                ->groupBy(['subject_id', 'subject_name', 'kafedra_id', 'kafedra_name']);

            $result = $subjectQuery->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('kafedra')) {
            $query = (new Query())
                ->select([
                    'subject.kafedra_id',
                    'tr.name AS kafedra_name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)'
                ])
                ->from('exam_student')
                ->join('JOIN', 'subject', 'subject.id = exam_student.subject_id')
                ->innerJoin('exam', 'exam_student.exam_id = exam.id')
                ->innerJoin('translate tr', 'subject.kafedra_id = tr.model_id AND tr.language=\'uz\' and tr.table_name =\'kafedra\'')
                ->where(['exam.archived' => 0])
                ->groupBy(['subject.kafedra_id', 'tr.name']);

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('teacher')) {
            $query = (new Query())
                ->select([
                    'teacher_access.user_id as teacher_user_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'subject.kafedra_id',
                    'tr.name AS kafedra_name',

                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)',
                    'all_time' => 'SUM(checking_time)',
                ])
                ->from('exam_student')
                ->innerJoin('teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
                ->innerJoin('profile', 'profile.user_id = teacher_access.user_id')
                ->join('JOIN', 'subject', 'subject.id = exam_student.subject_id')
                ->innerJoin('exam', 'exam_student.exam_id = exam.id')
                ->where(['exam.archived' => 0])
                ->innerJoin('translate tr', 'subject.kafedra_id = tr.model_id AND tr.language=\'uz\' and tr.table_name =\'kafedra\'')

                ->groupBy([
                    'teacher_access.user_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                ]);

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        $query = (new Query())
            ->select([
                'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                'all' => 'COUNT(*)'
            ])
            ->from('exam_student')
            ->innerJoin('exam', 'exam_student.exam_id = exam.id')
            ->where(['exam.archived' => 0]);

        $result = $query->all();

        return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    }


    public function actionBallMysql($lang)
    {
        if (null !== Yii::$app->request->get('faculty')) {
            $query = (new Query())
                ->select([
                    'exam.faculty_id',
                    'tr.name AS faculty_name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)'
                ])
                ->from('exam_student')
                ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
                ->join('JOIN', 'translate tr', 'exam.faculty_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'faculty\'')
                ->groupBy('exam.faculty_id');

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('subject')) {
            $query = (new Query())
                ->select([
                    'exam.subject_id',
                    'tr.name AS subject_name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)'
                ])
                ->from('exam_student')
                ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
                ->join('JOIN', 'translate tr', 'exam.subject_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'subject\'')
                ->groupBy('exam.subject_id');

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('kafedra')) {
            $query = (new Query())
                ->select([
                    'exam.subject_id',
                    'tr.name AS subject_name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)'
                ])
                ->from('exam_student')
                ->join('JOIN', 'exam', 'exam_student.exam_id = exam.id')
                ->join('JOIN', 'translate tr', 'exam.subject_id = tr.model_id AND tr.`language`=\'uz\' and tr.table_name =\'subject\'')
                ->groupBy('exam.subject_id');

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        if (null !== Yii::$app->request->get('teacher')) {
            $query = (new Query())
                ->select([
                    'teacher_access.user_id as teacher_user_id',
                    'profile.last_name',
                    'profile.first_name',
                    'profile.middle_name',
                    'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                    'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                    'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                    'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                    'all' => 'COUNT(*)'
                ])
                ->from('exam_student')
                ->join('JOIN', 'teacher_access', 'exam_student.teacher_access_id = teacher_access.id')
                ->join('JOIN', 'profile', 'profile.user_id = teacher_access.user_id')
                ->groupBy('teacher_access.user_id');

            $result = $query->all();

            return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
        }

        $query = (new Query())
            ->select([
                'two' => 'COUNT(CASE WHEN main_ball < 56 THEN 1 END)',
                'three' => 'COUNT(CASE WHEN main_ball >= 56 AND main_ball <= 70 THEN 1 END)',
                'four' => 'COUNT(CASE WHEN main_ball >= 71 AND main_ball <= 85 THEN 1 END)',
                'five' => 'COUNT(CASE WHEN main_ball > 85 THEN 1 END)',
                'all' => 'COUNT(*)'
            ])
            ->from('exam_student');

        $result = $query->all();

        return $this->response(1, _e('Success'), $result, null, ResponseStatus::OK);
    }

    public function actionAct($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            $result = ExamStudent::actItemWithCreate($model, Yii::$app->request->post());
        } else {
            $result = ExamStudent::actItem($model, Yii::$app->request->post());
        }

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while making act.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionView($lang, $id)
    {
        if (isRole('teacher')) {
            $model = ExamNoStudent::find()
                ->andWhere(['id' => $id, 'is_deleted' => 0])
                ->one();
        } else {
            $model = ExamStudent::find()
                ->andWhere(['id' => $id, 'is_deleted' => 0])
                ->one();
        }

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->type == null) {
            // if ($model->type > 0) {
            $model->ball = $model->allBall;
            $model->is_checked = $model->isChecked;
            $model->is_checked_full = $model->isCheckedFull;
            $model->has_answer = $model->hasAnswer;
            $model->getControlBallCorrect();
            $model->update();
            // }
        }

        if (isRole("teacher")) {
            if ($model->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionReexam($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        $model->act = 0;
        $model->status = 10;
        $model->save(false);
        return $this->response(0, _e('OOPs.'), $model->getErrors(), $model, ResponseStatus::OK);



        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        // return $post;

        $resultWriteReason = ExamStudentReexam::createItem($post, $model->id);
        if (is_array($resultWriteReason)) {
            return $this->response(0, _e('Error on Creating ExamStudentReexam.'), null, $resultWriteReason, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $result = ExamStudent::deleteMK($model);

        // dd($result);s
        // $resul
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' succesfully cleared for next attempt.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('Error on deleting.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        return $this->response(0, _e('OOPs.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        // $model->act = 0;
        // $model->save(false);
        // return $this->response(0, _e('OOPs.'), null, null, ResponseStatus::BAD_REQUEST);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        // return $post;

        $resultWriteReason = ExamStudentReexam::createItem($post, $model->id);
        if (is_array($resultWriteReason)) {
            return $this->response(0, _e('Error on Creating ExamStudentReexam.'), null, $resultWriteReason, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $result = ExamStudent::deleteMK($model);

        // dd($result);s
        // $resul
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' succesfully cleared for next attempt.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('Error on deleting.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        return $this->response(0, _e('OOPs.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
