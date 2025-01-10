<?php

namespace api\controllers;

use api\resources\User;
use common\models\model\Building;
use common\models\model\FinalExam;
use common\models\model\FinalExamGroup;
use common\models\model\FinalExamTest;
use common\models\model\Profile;
use common\models\model\StudentGroup;
use common\models\Subject;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class FinalExamTestController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'final_exam_test';
    public $controller_name = 'FinalExamTest';

    public function actionIndex($lang)
    {
        $model = new FinalExamTest();

        $query = FinalExamTest::find()
            ->with(['profile'])
            ->where(['final_exam_test.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = final_exam_test.user_id');

        $finalExamId = Yii::$app->request->get('final_exam_id');
        $finalExam = FinalExam::findOne($finalExamId);
        $userId = current_user_id();

        if ($finalExam) {

            $groupId = Yii::$app->request->get('group_id');

            if (isset($groupId)) {
                $query->andWhere([
                    'in',
                    'student_id',
                     StudentGroup::find()
                        ->select('student_id')
                        ->where([
                            'status' => 1,
                            'is_deleted' => 0,
                            'group_id' => $groupId,
                            'edu_semestr_id' => $finalExam->edu_semestr_id
                        ])
                ]);
            }

            if (isRole('dean')) {
                $faculty = get_dean();
                if ($finalExam->faculty_id != $faculty->id) {
                    $query->andWhere(['final_exam_test.is_deleted' => -1]);
                }
            } elseif (isRole('teacher') || isRole('tutor')) {
                if ($finalExam->user_id != $userId) {
                    $query->andWhere(['final_exam_test.is_deleted' => -1]);
                }
            }
            $query->andWhere(['final_exam_test.final_exam_id' => $finalExam->id]);
        } else {
            $query->andWhere(['final_exam_test.is_deleted' => -1]);
        }


        $profile = new Profile();
        $user = new User();
        $queryfilter = Yii::$app->request->get('profile-filter-like');
        if (isset($queryfilter)) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter));
            if ($queryfilter) {
                foreach ($queryfilter as $attributeq => $word) {
                    if (in_array($attributeq, $profile->attributes())) {
                        $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                    }
                    if (in_array($attributeq, $user->attributes())) {
                        $query = $query->andFilterWhere(['like', 'users.' . $attributeq, '%' . $word . '%', false]);
                    }
                }
            }
        }

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionAdd($lang , $id)
    {
        $model = FinalExamTest::findOne([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTest::add($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionUpdate($lang , $id)
    {
        $model = FinalExamTest::findOne([
            'id' => $id,
            'is_deleted' => 0,
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $t = true;
        $finalExam = $model->finalExam;
        if (isRole('dean')) {
            $faculty = get_dean();
            if ($faculty != null) {
                if ($finalExam->faculty_id != $faculty->id) {
                    $t = false;
                }
            } else {
                $t = false;
            }
        }

        if (isRole('teacher') || isRole('tutor')) {
            if ($finalExam->user_id != current_user_id()) {
                $t = false;
            }
        }

        if (!$t) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $result = FinalExamTest::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


}