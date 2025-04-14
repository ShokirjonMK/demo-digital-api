<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\ExamControlStudent;
use common\models\model\Faculty;
use common\models\model\Subject;
use Yii;
use yii\rest\ActiveController;

class ExamControlStudentController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControlStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control_student';
    public $controller_name = 'ExamControlStudent';


    public function actionIndex($lang)
    {
        $model = new ExamControlStudent();

        $query = $model->find()
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = {$model->tableName()}.id and tr.table_name = '{$model->tableName()}'")
            ->andWhere([$model->tableName() . '.archived' => 0])
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        if (null !== Yii::$app->request->get('kafedra_id')) {
            $query->andWhere([
                'in', $model->tableName() . '.subject_id',
                Subject::find()->select('id')->where(['kafedra_id' => Yii::$app->request->get('kafedra_id')])
            ]);
        }

        if (null !== Yii::$app->request->get('noChek')) {
            $query->andWhere([
                'or',
                ['and', ['not' => [$model->tableName() . '.appeal' => null]], [$model->tableName() . '.appeal_status' => null]],
                ['and', ['not' => [$model->tableName() . '.appeal2' => null]], [$model->tableName() . '.appeal2_status' => null]],
            ]);
        }

        if (null !== Yii::$app->request->get('appeal')) {
            $query->andWhere(['or', ['appeal' => Yii::$app->request->get('appeal')], ['appeal2' => Yii::$app->request->get('appeal')]]);
        }

        if (null !== Yii::$app->request->get('nocheckappeal')) {
            $query->andWhere(['not' => [$model->tableName() . '.appeal' => null]], [$model->tableName() . '.appeal_status' => null]);
        }

        if (null !== Yii::$app->request->get('nocheckappeal2')) {
            $query->andWhere(['not' => [$model->tableName() . '.appeal2' => null]], [$model->tableName() . '.appeal2_status' => null]);
        }

        if (null !== Yii::$app->request->get('allAppeal')) {
            $query->andWhere(['or', ['not', ['appeal' => null]], ['not', ['appeal2' => null]]]);
        }

        if (isRole("student")) {
            $query->andWhere(['in', $model->tableName() . '.student_id', $this->student()]);
        }

        if ((isRole("mudir") || isRole("teacher")) && !isRole('dean')) {
            $query->andWhere(['in', $model->tableName() . '.subject_id', $this->subject_ids()]);
        }

        // Apply additional filters and sorting
        $query = $this->filterAll($query, $model);
        $query = $this->sort($query);

        // Print the raw SQL for debugging
        // dd($query->createCommand()->getRawSql());

        // Fetch data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }


    public function actionCreate($lang)
    {
        $model = new ExamControlStudent();
        $post = Yii::$app->request->post();
        $data = [];
        if (isRole('student')) {
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::createItem($model, $data);
        } else {
            // if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::createItem($model, $post);
        }
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCheck($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data = [];
        $post = Yii::$app->request->post();


        // ball o'zgartirishni cheklash
        //  if (isset($post['ball'])) {
        //     if (!is_null($model->ball) || !($model->ball == 0)) {
        //       if ($post['ball'] != $model->ball)
        //              return $this->response(0, _e('Can not change ball.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        //      }
        //  }

        // if (isset($post['ball2'])) {
        // if (!is_null($model->ball2) || !($model->ball2 == 0)) {
        // if ($post['ball2'] != $model->ball2)
        // return $this->response(0, _e('Can not change ball2.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        //      }
        // }


        if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
        if (isset($post['upload2_file'])) unset($post['upload2_file']);
        if (isset($post['upload_file'])) unset($post['upload_file']);
        if (isset($post['answer2'])) unset($post['answer2']);
        if (isset($post['answer'])) unset($post['answer']);
        if (isset($post['main_ball'])) unset($post['main_ball']);

        $this->load($model, $post);
        $result = ExamControlStudent::updateItem($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionUpdate($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data = [];
        $post = Yii::$app->request->post();


        // ball o'zgartirishni cheklash
        // if (isset($post['ball'])) {
        //   if (!is_null($model->ball) || !($model->ball == 0)) {
        //          if ($post['ball'] != $model->ball)
        //           return $this->response(0, _e('Can not change ball.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        //      }
        //  }

        //  if (isset($post['ball2'])) {
        //      if (!is_null($model->ball2) || !($model->ball2 == 0)) {
        //          if ($post['ball2'] != $model->ball2)
        //              return $this->response(0, _e('Can not change ball2.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        //      }
        //  }



        // if (isset($post['ball2'])) {
        //     if (!is_null($model->ball2)) {
        //         return $this->response(0, _e('Can not change ball2.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        //     }
        // }

        if (isRole('student')) {


            // if (isset($post['upload_file'])) {
            //     if (!is_null($model->answer_file)) {
            //         return $this->response(0, _e('Faqat yuklanmaganlar uchun1.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            //     }
            // }

            // if (isset($post['upload2_file'])) {
            //     if (!is_null($model->answer2_file)) {
            //         return $this->response(0, _e('Faqat yuklanmaganlar uchun2.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            //     }
            // }

            // if (isset($post['answer2'])) {
            //     if (!is_null($model->answer2)) {
            //         return $this->response(0, _e('Faqat javob yozmaganlar uchun1.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            //     }
            // }

            // if (isset($post['answer'])) {
            //     if (!is_null($model->answer)) {
            //         return $this->response(0, _e('Faqat javob yozmaganlar uchun2.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            //     }
            // }

            if ($model->student_id != $this->student()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours'), ResponseStatus::UPROCESSABLE_ENTITY);
            }
            if (isset($post['exam_control_id'])) $data['exam_control_id'] = $post['exam_control_id'];
            if (isset($post['upload2_file'])) $data['upload2_file'] = $post['upload2_file'];
            if (isset($post['upload_file'])) $data['upload_file'] = $post['upload_file'];
            if (isset($post['answer2'])) $data['answer2'] = $post['answer2'];
            if (isset($post['answer'])) $data['answer'] = $post['answer'];

            $this->load($model, $data);
            $result = ExamControlStudent::updateItem($model, $data);
        } else {
            if (isset($post['exam_control_id'])) unset($post['exam_control_id']);
            if (isset($post['upload2_file'])) unset($post['upload2_file']);
            if (isset($post['upload_file'])) unset($post['upload_file']);
            if (isset($post['answer2'])) unset($post['answer2']);
            if (isset($post['answer'])) unset($post['answer']);
            if (isset($post['main_ball'])) unset($post['main_ball']);

            $this->load($model, $post);
            $result = ExamControlStudent::updateItem($model, $post);
        }

        // $this->load($model, $post);
        // $result = ExamControlStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
    public function actionChangeBallWithFile($lang, $id = null)
    {
        $model = ExamControlStudent::findOne($id);
        $post = Yii::$app->request->post();

        if (!$model) {
            $model = new ExamControlStudent();
            $this->load($model, $post);
            $result = ExamControlStudent::createItem($model, $post, 1);

            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }


        $result = ExamControlStudent::changeBallWithFile($model, $post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAppeal($lang, $id)
    {
        $model = ExamControlStudent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $data = [];
        $post = Yii::$app->request->post();

        if (isRole('student')) {
            if ($model->student_id != $this->student()) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not yours'), ResponseStatus::UPROCESSABLE_ENTITY);
            }

            // $this->load($model, $post);
            $result = ExamControlStudent::appealNew($model, $post);
        } else {

            // $this->load($model, $post);
            $result = ExamControlStudent::appealCheck($model, $post);
        }

        // $this->load($model, $post);
        // $result = ExamControlStudent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControlStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControlStudent::find()
            // ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();


        if ($model->delete()) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(0, _e('Not delete.'), $model, null, ResponseStatus::OK);
    }
}
