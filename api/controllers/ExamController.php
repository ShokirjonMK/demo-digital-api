<?php

namespace api\controllers;

use common\models\model\EduSemestr;
use common\models\model\Translate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestrSubject;
use common\models\model\Exam;
use common\models\model\ExamConclution;
use common\models\model\ExamSemeta;
use common\models\model\ExamSupervisor;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use common\models\model\TeacherAccess;
use yii\caching\DbDependency;

class ExamController extends ApiActiveController
{
    public $modelClass = 'api\resources\Exam';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam';
    public $controller_name = 'Exam';

    public function actionGeneratePasswords($lang)
    {
        $post = Yii::$app->request->post();
        $result = Exam::generatePasswords($post);

        if (!is_array($result)) {
            return $this->response(1, _e('Passwords successfully generated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
        return $result;
    }

    public function actionGetPasswords($lang)
    {
        $post = Yii::$app->request->post();
        $result = Exam::getPasswords($post);
        if (is_array($result)) {
            if ($result['is_ok']) {
                // unset($result['is_ok']);
                return $this->response(1, _e('Passwords for students for this exam'), $result, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        return $result;
    }

    public function actionIndex($lang)
    {
        $model = new Exam();
        $student = Student::findOne(['user_id' => current_user_id()]);
        $eduSmesterId = Yii::$app->request->get('edu_semestr_id');

        $query = $model->find()->with(['infoRelation']);

        // Apply common filters
        $query = $query
            ->andWhere([$model->tableName()  . '.is_deleted' => 0])
            ->andWhere([$model->tableName()  . '.archived' => 0])
            ->leftJoin("translate tr", "tr.model_id = " . $model->tableName() . ".id and tr.table_name = '" . $model->tableName() . "'")
            ->groupBy($model->tableName()  . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        // Apply 'no' filter if provided
        if (Yii::$app->request->get('no')) {
            $query->andFilterWhere([
                'in',
                $model->tableName()  . '.student_id',
                [
                    10622,
                    10619,
                    4998,
                    10191,
                    10746,
                    10680,
                    10748,
                    10585,
                    10222,
                    10221,
                    10239,
                    10741,
                    10255,
                    10254,
                    10527,
                    10528
                ]
            ]);
        }

        if (Yii::$app->request->get('supervisor')) {
            $query->andFilterWhere([
                'in',
                $model->tableName()  . '.id',
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

        // Apply 'statuses' filter if provided
        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('statuses')));
        if ($statuses) {
            $query->andFilterWhere([
                'in',
                $model->tableName()  . '.status',
                $statuses
            ]);
        }

        // Apply 'subject_id' filter if provided
        $subjectId = Yii::$app->request->get('subject_id');
        if ($subjectId) {
            $query = $query->andFilterWhere(['in', 'edu_semestr_subject_id', EduSemestrSubject::find()
                ->where(['subject_id' => $subjectId])
                ->andWhere(['is_deleted' => 0])
                ->select('id')]);
        }

        // Apply filters based on user roles
        if ($student && isRole('student')) {
            if (isset($eduSmesterId)) {
                $query = $query->andWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()
                        ->andWhere(['edu_semestr_id' => $eduSmesterId])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            } else {
                $query = $query->andWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()
                        ->where(['in', 'edu_semestr_id', EduSemestr::find()
                            ->where(['edu_plan_id' => $student->edu_plan_id])
                            ->andWhere(['is_deleted' => 0])
                            ->select('id')])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            }
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

        if (isRole('teacher') && !isRole('mudir')) {
            $query = $query->andFilterWhere([
                'in',
                'edu_semestr_subject_id',
                EduSemestrSubject::find()
                    ->where(['subject_id' => $this->teacher_access(1, ['subject_id'])])
                    ->andWhere(['is_deleted' => 0])
                    ->select('id')
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

        if (isRole('mudir')) {
            // Apply 'mudir' specific filters
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {

                // $query = $query->andFilterWhere([
                //     'or',
                //     [
                //         'and',
                //         ['in', 'edu_semestr_subject_id', Subject::find()
                //             ->where(['kafedra_id' => $k['UserAccess']->table_id, 'is_deleted' => 0])
                //             ->select('id')],
                //         ['is_deleted' => 0],
                //     ],
                //     [
                //         'and',
                //         ['in', 'edu_semestr_subject_id', $this->teacher_access(1, ['subject_id'])],
                //         ['is_deleted' => 0],
                //     ],
                // ]);


                $query = $query->andFilterWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()->where([
                        'in',
                        'subject_id',
                        Subject::find()->where(['kafedra_id' => $k['UserAccess']->table_id, 'is_deleted' => 0])->select('id')
                    ])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);

                //

            } elseif ($k['status'] == 2) {
                $query = $query->andFilterWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()
                        ->where(['subject_id' => $this->teacher_access(1, ['subject_id'])])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            }
        } else {
            // Apply role-based filters
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $model->tableName() . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $model->tableName() . '.faculty_id' => -1
                ]);
            }
        }

        // Apply additional filtering, sorting, and data retrieval logic
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        // data caching
        // $query = $query->cache(3600, new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM exam']));

        // rawsql($query);
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionIndex11($lang)
    {
        $model = new Exam();
        $student = Student::findOne(['user_id' => current_user_id()]);
        // return $student;
        $eduSmesterId = Yii::$app->request->get('edu_semestr_id');

        $query = $model->find()->with(['infoRelation']);

        $query = $query->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (Yii::$app->request->get('no')) {
            $query->andFilterWhere([
                'not in',
                $this->table_name . '.id',
                [
                    483,
                    439,
                    438,
                    437,
                    432,
                    431,
                    428,
                    425,
                    424,
                    423,
                    422,
                    421,
                    420,
                    419,
                    415,
                    414,
                    413,
                    412,
                    411,
                    410,
                    409,
                    408,
                    387,
                    385,
                    384,
                    379,
                    378,
                    377,
                    374,
                    373,
                    371,
                    370,
                    369,
                    365,
                    364,
                    363,
                    362,
                    361,
                    360,
                    355,
                    354,
                    353,
                    351,
                    323,
                    321,
                    300,
                    299,
                    298,
                    297,
                    296,
                    295,
                    288,
                    287,
                    286,
                    285,
                    284,
                    283,
                    282,
                    281,
                    280,
                    279,
                    278,
                    277,
                    276,
                    275,
                    274,
                    273,
                    272
                ]
            ]);
            $query->andFilterWhere([
                '>=',
                $this->table_name . '.id',
                272
            ]);
            $query->andFilterWhere([
                '<=',
                $this->table_name . '.id',
                515
            ]);

            // exam_id>=272 and exam_id<=515 
        }

        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('statuses')));

        if ($statuses) {
            $query->andFilterWhere([
                'in',
                $this->table_name . '.status',
                $statuses
            ]);
        }

        $subjectId = Yii::$app->request->get('subject_id');
        if ($subjectId) {
            $query = $query->andFilterWhere(['in', 'edu_semestr_subject_id', EduSemestrSubject::find()
                ->where(['subject_id' => $subjectId])
                ->andWhere(['is_deleted' => 0])
                ->select('id')]);
        }

        if ($student && isRole('student')) {
            //            dd($student->edu_plan_id);
            if (isset($eduSmesterId)) {
                $query = $query->andWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()
                        ->andWhere(['edu_semestr_id' => $eduSmesterId])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            } else {

                $query = $query->andWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()
                        ->where(['in', 'edu_semestr_id', EduSemestr::find()
                            ->where(['edu_plan_id' => $student->edu_plan_id])
                            ->andWhere(['is_deleted' => 0])
                            ->select('id')])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            }

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $query = $query->andFilterWhere([
                'in',
                'edu_semestr_subject_id',
                EduSemestrSubject::find()
                    ->where(['subject_id' => $this->teacher_access(1, ['subject_id'])])
                    ->andWhere(['is_deleted' => 0])
                    ->select('id')
            ]);

            // $query = $query->andWhere([
            //     $this->table_name . '.status' => Exam::STATUS_DISTRIBUTED
            // ]);

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (isRole('mudir')) {
            /*  is Self  */
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {
                $query = $query->andFilterWhere([
                    'in',
                    'edu_semestr_subject_id',
                    EduSemestrSubject::find()->where([
                        'in',
                        'subject_id',
                        Subject::find()->where(['kafedra_id' => $k['UserAccess']->table_id, 'is_deleted' => 0])->select('id')
                    ])
                        ->andWhere(['is_deleted' => 0])
                        ->select('id')
                ]);
            } elseif ($k['status'] == 2) {
                $query->andFilterWhere([
                    $this->table_name . '.faculty_id' => -1
                ]);
            }
            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
            /*  is Self  */
        } else {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $this->table_name . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $this->table_name . '.faculty_id' => -1
                ]);
            }
            /*  is Self  */

            // filter
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // data
            $data = $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }
    }

    public function actionCreate($lang)
    {
        $model = new Exam();
        $post = Yii::$app->request->post();
        // $post['duration'] =  strtotime($post['duration']);

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        $this->load($model, $post);
        if (isset($post->start)) {
            $model->start = date('Y-m-d H:i:s', strtotime($post->start));
        }
        if (isset($post->finish)) {
            $model->finish = date('Y-m-d H:i:s', strtotime($post->finish));
        }

        $result = Exam::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Exam::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $post = Yii::$app->request->post();

        if (isset($post->start)) {
            $model->start = date('Y-m-d H:i:s', strtotime($post->start));
        }
        if (isset($post->finish)) {
            $model->finish = date('Y-m-d H:i:s', strtotime($post->finish));
        }
        if (isset($post['appeal_start'])) {
            $post['appeal_start'] = strtotime($post['appeal_start']);
        }
        if (isset($post['appeal_finish'])) {
            $post['appeal_finish'] = strtotime($post['appeal_finish']);
        }

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        $this->load($model, $post);
        // return $model;
        $result = Exam::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // return $model->eduSemestrSubject->subject->kafedra_id;

        if (isRole('mudir')) {
            /*  is Self  */
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID, 2);
            if ($k['status'] == 1) {
                if (!($model->eduSemestrSubject->subject->kafedra_id == $k['UserAccess']->table_id)) {
                    return $this->response(0, _e('You do not have acceess.'), null, null, ResponseStatus::FORBIDDEN);
                }
            }
            /*  is Self  */
        }
        // else {
        // /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     if ($model->facuty_id != $t['UserAccess']->table_id) {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        //     }
        // } elseif ($t['status'] == 2) {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        // }
        // /*  is Self  */
        // }


        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionDistribution($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Exam::distribution($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' succesfully distributed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAppealDistribution($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Exam::appealDistribution($model);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' Appeals succesfully distributed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAd($lang, $id)
    {
        $model = Exam::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // if ($model->status == Exam::STATUS_ANNOUNCED) {
        //     $model->status = Exam::STATUS_DISTRIBUTED;
        // } else {
        //     $model->status = Exam::STATUS_ANNOUNCED;
        //     $model->status_appeal = Exam::STATUS_APPEAL_ACTIVE;

        //     $model->appeal_start = time();
        //     $model->appeal_finish = time() + 72 * 60 * 60;
        // }
        if ($model->status == Exam::STATUS_ANNOUNCED) {
            // $model->status = Exam::STATUS_DISTRIBUTED;
            return $this->response(1, _e('Bekor qilish yoq'), $model, null, ResponseStatus::OK);
        } else {
            $model->status = Exam::STATUS_ANNOUNCED;
            $model->status_appeal = Exam::STATUS_APPEAL_ACTIVE;

            $model->appeal_start = time();
            $model->appeal_finish = time() + 72 * 60 * 60;
        }

        // return $model;
        if ($model->save()) {
            return $this->response(1, _e($this->controller_name . ' succesfully announced.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConclution($lang)
    {
        $model = new ExamConclution();
        $post = Yii::$app->request->post();
        if (isset($post['id'])) {
            $model = ExamConclution::find()
                ->andWhere(['id' => $post['id'], 'is_deleted' => 0])
                ->one();
            $this->load($model, $post);

            $result = ExamConclution::createItem($model, $post);
            if (!is_array($result)) {
                return $this->response(1, _e('ExamConclution successfully updated.'), $model, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

        $this->load($model, $post);

        $result = ExamConclution::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('ExamConclution successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConclutionGet($lang)
    {
        $model = new ExamConclution();
        $query = $model->find();
        $query->filterWhere(['is_deleted' => 0]);
        if (!isRole('admin')) {
            $query->filterWhere(['created_by' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);
        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionConclutionUpdate($lang, $id)
    {
        $model = new ExamConclution();
        $post = Yii::$app->request->post();
        $model = ExamConclution::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $this->load($model, $post);

        $result = ExamConclution::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('ExamConclution successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionConclutionDelete($lang, $id)
    {
        $model = new ExamConclution();
        $post = Yii::$app->request->post();
        $model = ExamConclution::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e('ExamConclution succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionSupervisor($lang)
    {
        $model = new ExamSupervisor();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = ExamSupervisor::createItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e('ExamSupervisor successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionSupervisorDelete($lang, $id)
    {
        $model = ExamSupervisor::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->delete();

            return $this->response(1, _e('ExamSupervisor succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
