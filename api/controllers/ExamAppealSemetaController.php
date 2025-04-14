<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestrSubject;
use common\models\model\Exam;
use common\models\model\ExamAppealSemeta;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Subject;

class ExamAppealSemetaController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamAppealSemeta';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_appeal_semeta';
    public $controller_name = 'ExamAppealSemeta';

    public function actionIndex($lang)
    {
        $model = new ExamAppealSemeta();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->andFilterWhere(['like', 'question', Yii::$app->request->get('query')]);


        /*  is Self  */
        $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        if ($t['status'] == 1) {
            $query = $query->leftJoin('exam', 'exam.id = exam_appeal_semeta.exam_id',)
                ->andWhere(['exam.faculty_id' => $t['UserAccess']->table_id]);
        } elseif ($t['status'] == 2) {
            $query->andFilterWhere([
                'exam_id' => -1
            ]);
        }
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
        $model = new ExamAppealSemeta();
        $post = Yii::$app->request->post();
        $errors = [];

        if (!isRole('mudir')) {
            /*  is Self  */
            $t = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $exam = Exam::findOne($post['exam_id'] ?? null);
                if ($exam) {
                    $department = $t['UserAccess']->department;
                    if ($department['user_access_type_id'] == Kafedra::USER_ACCESS_TYPE_ID) {
                        if ($exam->faculty_id != $department['model']->faculty_id) {
                            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
                        }
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
            $result = ExamAppealSemeta::createItems($post);

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
            $result = ExamAppealSemeta::createItem($model, $post);
        }

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamAppealSemeta::findOne($id);
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

        if (($model->status == ExamAppealSemeta::STATUS_IN_CHECKING || $model->status == ExamAppealSemeta::STATUS_CONFIRMED) && isRole('mudir')) {
            return $this->response(0, _e('Can not change.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = ExamAppealSemeta::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamAppealSemeta::find()
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
        $model = ExamAppealSemeta::find()
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
