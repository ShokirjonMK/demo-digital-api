<?php

namespace api\controllers;

use common\models\model\EduSemestrSubject;
use common\models\model\EduYear;
use common\models\model\StudentSemestrSubject;
use common\models\model\TimeTable1;
use common\models\model\TimetableDate;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\Subject;
use common\models\model\TeacherAccess;

class EduSemestrSubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestrSubject';

    public function actions()
    {
        return [];
    }
    public $table_name = 'edu_semestr_subject';
    public $controller_name = 'EduSemestrSubject';

    const REQUIRED = 1;
    const OPTIONAL = 2;

    public function actionIndex($lang)
    {
        $model = new EduSemestrSubject();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        if (isRole("teacher")) {
            $teacherAccessSubjectIds = TeacherAccess::find()
                ->select('subject_id')
                ->where(['user_id' => current_user_id(), 'is_deleted' => 0]);

            $timeTable = TimetableDate::find()
                ->select('edu_semestr_subject_id')
                ->where([
                    'user_id' => current_user_id(),
                    'group_id' => Yii::$app->request->get('group_id'),
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->andWhere(['in' , 'subject_id' , $teacherAccessSubjectIds])
                ->andFilterWhere(['edu_year_id' => activeYearId()]);

             $query->andWhere(['in' , 'id' , $timeTable]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
//        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        $model = new EduSemestrSubject();
        $post = Yii::$app->request->post();

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            // EduSemestr -> EduPlan faculty_id
            $eduSemester = EduSemestr::findOne($post['edu_semestr_id'] ?? null);
            if ($eduSemester) {
                if ($eduSemester->eduPlan->faculty_id != $t['UserAccess']) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                }
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $this->load($model, $post);
        $result = EduSemestrSubject::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
//        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->eduSemestr->edu_year_id == 7 || $model->eduSemestr->edu_year_id == 8) {
//            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        $post = Yii::$app->request->post();
        $edu_semestr = EduSemestr::findOne($model->edu_semestr_id);

        if (isset($edu_semestr))
        {
            if (isset($post['subject_type_id']) && !empty($post['subject_type_id']))
            {
                if ($model->subject_type_id != $post['subject_type_id'])
                {
                    if ($model->subject_type_id == null)
                    {
                        if ($post['subject_type_id']==self::REQUIRED) {
                            $edu_semestr->required_subject_count = $edu_semestr->required_subject_count + 1;
                        }elseif ($post['subject_type_id']==self::OPTIONAL) {
                            $edu_semestr->optional_subject_count = $edu_semestr->optional_subject_count + 1;
                        }
                    } else {
                        if ($post['subject_type_id']==self::REQUIRED) {
                            $edu_semestr->required_subject_count = $edu_semestr->required_subject_count + 1;
                            $edu_semestr->optional_subject_count = $edu_semestr->optional_subject_count - 1;
                        }elseif ($post['subject_type_id']==self::OPTIONAL) {
                            $edu_semestr->optional_subject_count = $edu_semestr->optional_subject_count + 1;
                            $edu_semestr->required_subject_count = $edu_semestr->required_subject_count - 1;
                        }
                    }
                }
            }
            if (empty($post['subject_type_id'])){
                $post['subject_type_id'] = $model->subject_type_id;
            }
            $edu_semestr->save(false);
        }

        $this->load($model, $post);
        $result = EduSemestrSubject::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Edu Semestr Subject successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = EduSemestrSubject::find()
            ->andWhere(['id' => $id , 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('student')) {
            $studentUser = current_user_id();
            $isCheck = StudentSemestrSubject::findOne([
                'edu_semestr_subject_id' => $model->id,
                'status' => 1,
                'is_deleted' => 0,
                'student_user_id' => $studentUser
            ]);
            if (!$isCheck) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        } else {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                }
            } elseif ($t['status'] == 2) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
            /*  is Self  */
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $errors = [];
        $model = EduSemestrSubject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model

        if ($model) {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            //            return $t;
            if ($t['status'] == 1) {
                if ($model->eduSemestr->eduPlan->faculty_id != $t['UserAccess']->table_id) {
                    $errors[] = _e('You don\'t have access');
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::FORBIDDEN);
                }
            } elseif ($t['status'] == 2) {
                $errors[] = _e('You don\'t have access or you are not admin');
                return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::FORBIDDEN);
            }
            /*  is Self  */

            $result = EduSemestrSubject::deleteItem($model);

            if (!is_array($result)) {
                return $this->response(1, _e('Edu Semestr Subject succesfully removed.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
