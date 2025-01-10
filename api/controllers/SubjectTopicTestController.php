<?php

namespace api\controllers;


use common\models\model\StudentTopicPermission;
use common\models\model\StudentTopicResult;
use common\models\model\StudentTopicTestAnswer;
use common\models\model\SubjectTopic;
use Yii;
use base\ResponseStatus;


class SubjectTopicTestController extends ApiActiveController
{

    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang) {
        $model = new StudentTopicResult();

        $query = $model->find()
            ->where(['is_deleted' => 0]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionTopicTest($lang) {
        $post = Yii::$app->request->post();

        if (isRole('student')) {

            $result = SubjectTopic::topicTest($post);

            if ($result['is_ok']) {
                $data = [];
                $query = StudentTopicResult::findOne([
                    'subject_topic_id' => $post['topic_id'],
                    'user_id' => current_user_id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ]);
                $data['result'] = [
                    "id" => $query->id,
                    "start_time" => $query->start_time,
                    "current_time" => $query->now,
                    "status" => $query->status,
                ];
                $data['questions'] = StudentTopicTestAnswer::find()
                    ->where([
                        'student_topic_result_id' => $query->id,
                        'subject_topic_id' => $post['topic_id'],
                        'user_id' => current_user_id(),
                        'status' => 1,
                        'is_deleted' => 0,
                    ])->all();

                if (count($data) == 0) {
                    $errors = ['questions' => _e('Question not found')];
                    return $this->response(0, _e('There is an error occurred while processing.'),null, $errors, ResponseStatus::NO_CONTENT);
                }
                return $this->response(1, _e('Success'), $data);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    }

    public function actionAnswer($lang) {
        $post = Yii::$app->request->post();

        if (isRole('student')) {

            $result = SubjectTopic::answer($post);

            if ($result['is_ok']) {
                $data = StudentTopicTestAnswer::find()
                    ->where([
                        'student_topic_result_id' => $result['data'],
                        'user_id' => current_user_id(),
                        'status' => 1,
                        'is_deleted' => 0,
                    ])
                    ->all();
//                $query = StudentTopicResult::findOne([
//                    'id' => $result['data'],
//                    'user_id' => current_user_id(),
//                    'is_deleted' => 0,
//                ]);
//                $data['result'] = [
//                    "id" => $query->id,
//                    "time" => $query->now,
//                    "status" => $query->status,
//                ];
                return $this->response(1, _e('Success'), $data);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
    }

    public function actionFinish($lang) {

        $post = Yii::$app->request->post();

        if (isRole('student')) {

            $result = SubjectTopic::finish($post);
            if ($result['is_ok']) {
                $studentTopicResult = StudentTopicResult::findOne([
                    'id' => $result['data']
                ]);
                return $this->response(1, _e('Success'), $studentTopicResult);

            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }

    }

}
