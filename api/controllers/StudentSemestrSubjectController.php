<?php

namespace api\controllers;

use common\models\model\FinalExam;
use common\models\model\Student;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class StudentSemestrSubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_semestr_subject';
    public $controller_name = 'StudentSemestrSubject';

    public function actionIndex($lang)
    {
        $model = new StudentSemestrSubject();

        $query = $model->find()
            ->where(['is_deleted' => 0]);

        if (isRole('student')) {
            $query->andWhere(['student_user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }
}
