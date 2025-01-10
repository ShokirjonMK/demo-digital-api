<?php

namespace api\controllers;

use common\models\model\FinalExam;
use common\models\model\Student;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class StudentVedomstController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_semestr_subject_vedomst';
    public $controller_name = 'StudentSemestrSubjectVedomst';

    public function actionIndex($lang)
    {
        $model = new StudentSemestrSubjectVedomst();

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

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();

        $model = StudentSemestrSubjectVedomst::findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = StudentSemestrSubjectVedomst::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

    }

    public function actionDelete($lang, $id)
    {
        $model = StudentSemestrSubjectVedomst::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model->vedomst == 1) {
            return $this->response(0, _e('The first return cannot be deleted.'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->update(false);

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

}
