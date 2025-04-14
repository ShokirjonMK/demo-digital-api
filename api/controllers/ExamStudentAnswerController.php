<?php

namespace api\controllers;

use common\models\model\ExamStudentAnswer;
use Yii;
use base\ResponseStatus;
use common\models\model\ExamStudent;
use common\models\model\ExamStudentAnswerForTeacher;
use common\models\model\Student;

class ExamStudentAnswerController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamStudentAnswer';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student_answer';
    public $controller_name = 'Exam Student Answer';

    public function actionGetQuestion($lang)
    {
        $data = [];
        $model = new ExamStudentAnswer();
        $post = Yii::$app->request->post();

        $result = ExamStudentAnswer::randomQuestions($post);

        if (isset($result['questions'])) {
            return $this->response(1, _e('Questions and times'), $result, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    }


    public function actionIndex($lang)
    {
        $errors = [];

        if (isRole('teacher')) {
            $model = new ExamStudentAnswerForTeacher();

            $query = $model->find()
                ->andWhere([$model->tableName() . '.is_deleted' => 0]);

            $examStudentIds = ExamStudent::find()
                ->where(['in', 'teacher_access_id', $this->teacher_access()])
                ->select('id');

            $query = $query
                ->andWhere(['in', $model->tableName() . '.exam_student_id', $examStudentIds]);


            // return
            // $examStudentIds->all();
        } else {
            $model = new ExamStudentAnswer();

            $query = $model->find()
                ->andWhere([$model->tableName() . '.is_deleted' => 0]);
        }

        $exam_student_id = Yii::$app->request->get('exam_student_id');

        if ($exam_student_id) {
            $examStudent = ExamStudent::findOne($exam_student_id);
            $query = $query->andFilterWhere([$model->tableName() . '.exam_student_id' => $exam_student_id]);
        }
        //  else {
        //     $errors[] = ['exam_student_id' => _e('Required')];
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        // }

        // if (!$examStudent) {
        //     $errors[] = ['examStudent' => _e('not found')];
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
        // }


        if (isRole('student')) {
            $student = Student::findOne(['user_id' => current_user_id()]);
            if (!$student) {
                $errors[] = ['Student' => _e('not found')];
                return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            // if ($student->id != $examStudent->student_id) {
            //     $errors[] = _e('This is not your exam');
            //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            // }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        $model = new ExamStudentAnswer();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = ExamStudentAnswer::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamStudentAnswer::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        // appeal_teacher_conclution bo'lsa $model->old_ball = $model->ball; qilib olish kerak
        if (isRole("teacher")) {
            if ($model->examStudent->teacherAccess->user_id != current_user_id()) {
                return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
            } else {
                $post['teacher_access_id'] = $model->examStudent->teacher_access_id;
            }
            $data = [];
            if (isset($post['teacher_conclusion'])) {
                $data['teacher_conclusion'] = $post['teacher_conclusion'];
            }
            if (isset($post['ball'])) {
                $data['ball'] = $post['ball'];
            }
            if (isset($post['subQuestionAnswersChecking'])) {
                $data['subQuestionAnswersChecking'] = $post['subQuestionAnswersChecking'];
            }

            $this->load($model, $data);
            $result = ExamStudentAnswer::updateItemTeacher($model, $data);

            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully saved.'), $model, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            $this->load($model, $post);

            $result = ExamStudentAnswer::updateItem($model, $post);
        }


        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    // public function actionAppeal($lang, $id)
    // {
    //     $model = ExamStudentAnswer::findOne($id);
    //     if (!$model) {
    //         return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    //     }

    //     $post = Yii::$app->request->post();
    //     // appeal_teacher_conclution bo'lsa $model->old_ball = $model->ball; qilib olish kerak
    //     if (isRole("teacher")) {
    //         if ($model->examStudent->teacherAccess->user_id != current_user_id()) {
    //             return $this->response(0, _e('You do not have access.'), null, null, ResponseStatus::FORBIDDEN);
    //         } else {
    //             $post['teacher_access_id'] = $model->examStudent->teacher_access_id;
    //         }
    //         $data = [];
    //         if (isset($post['appeal_teacher_conclusion'])) {
    //             $data['appeal_teacher_conclusion'] = $post['appeal_teacher_conclusion'];
    //         }
    //         if (isset($post['ball'])) {
    //             $data['ball'] = $post['ball'];
    //         }
    //         if (isset($post['subQuestionAnswersAppealChecking'])) {
    //             $data['subQuestionAnswersAppealChecking'] = $post['subQuestionAnswersAppealChecking'];
    //         }

    //         $this->load($model, $data);
    //         $result = ExamStudentAnswer::appealUpdateItemTeacher($model, $data);

    //         if (!is_array($result)) {
    //             return $this->response(1, _e($this->controller_name . ' successfully saved.'), $model, null, ResponseStatus::OK);
    //         } else {
    //             return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    //         }
    //     } else {
    //         $this->load($model, $post);
    //         $result = ExamStudentAnswer::appealUpdateItemTeacher($model, $post);

    //     }


    //     if (!is_array($result)) {
    //         return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
    //     } else {
    //         return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    //     }
    // }

    public function actionView($lang, $id)
    {
        $errors = [];
        $model = ExamStudentAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('student')) {
            $student = Student::findOne(['user_id' => current_user_id()]);
            if (!$student) {
                $errors[] = _e("Student not found");
                return $this->response(0, _e('Data not found.'), null, $errors, ResponseStatus::NOT_FOUND);
            }
            if ($model->student_id != $student->id) {
                $errors[] = _e("Student not found");
                return $this->response(0, _e('Data not found.'), null, $errors, ResponseStatus::NOT_FOUND);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        $model = ExamStudentAnswer::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
