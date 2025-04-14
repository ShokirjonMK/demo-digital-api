<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\Exam;
use common\models\model\ExamSemeta;
use common\models\model\Faculty;
class ExamSemetaController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamSemeta';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_semeta';
    public $controller_name = 'ExamSemeta';

    public function actionIndex($lang)
    {
        $model = new ExamSemeta();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andFilterWhere(['like', 'question', Yii::$app->request->get('query')]);


        /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     $query = $query->leftJoin('exam', 'exam.id = exam_semeta.exam_id',)
        //         ->andWhere(['exam.faculty_id' => $t['UserAccess']->table_id]);
        // } elseif ($t['status'] == 2) {
        //     $query->andFilterWhere([
        //         'exam_id' => -1
        //     ]);
        // }
        /*  is Self  */

        // /*  is Self  */
        // $t = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {

        //     $query = $query->leftJoin('exam', 'exam.id = exam_semeta.exam_id',)
        //         ->andWhere(['in', 'exam.edu_semestr_subject_id', EduSemestrSubject::find()
        //             ->select('id')
        //             ->where(['in', 'subject_id', Subject::find()
        //                 ->select('id')
        //                 ->where(['kafedra_id' => $t['UserAccess']->table_id])])]);
        // } elseif ($t['status'] == 2) {
        //     $query->andFilterWhere([
        //         'exam_id' => -1
        //     ]);
        // }
        // /*  is Self  */

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
        $model = new ExamSemeta();
        $post = Yii::$app->request->post();
        $errors = [];

        if (!isRole('mudir')) {
            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $exam = Exam::findOne($post['exam_id'] ?? null);
                if ($exam) {
                    if ($exam->faculty_id != $t['UserAccess']->table_id) {
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                    }
                } else {
                    $errors[] = ['exam_id' => _e('Exam is not found')];
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } elseif ($t['status'] == 2) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
            /*  is Self  */
        }

        if (isset($post['smetas'])) {
            $result = ExamSemeta::createItems($post);

            if (isset($result['status'])) {
                if ($result['status']) {
                    return $this->response(1, _e($this->controller_name . 's successfully created.'), $result['data'], null, ResponseStatus::CREATED);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
                }
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            $this->load($model, $post);
            $result = ExamSemeta::createItem($model, $post);
        }

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamSemeta::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->exam->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        if ($model->exam->status = Exam::STATUS_DISTRIBUTED) {
            return $this->response(0, _e('Modification is prohibited to prevent conflict.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (($model->status == ExamSemeta::STATUS_IN_CHECKING || $model->status == ExamSemeta::STATUS_CONFIRMED) && isRole('mudir')) {
            return $this->response(0, _e('Can not change.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamSemeta::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamSemeta::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            if ($model->exam->facuty_id != $t['UserAccess']->table_id) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
        } elseif ($t['status'] == 2) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        }
        /*  is Self  */

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamSemeta::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            /*  is Self  */
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                if ($model->exam->facuty_id != $t['UserAccess']->table_id) {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                }
            } elseif ($t['status'] == 2) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
            }
            /*  is Self  */

            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
