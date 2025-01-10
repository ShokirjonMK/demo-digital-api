<?php

namespace api\controllers;

use common\models\model\EduSemestrSubjectCategoryTime;
use Yii;
use api\resources\Job;
use base\ResponseStatus;
use common\models\JobInfo;
use common\models\model\Faculty;

class EduSemestrSubjectCategoryTimeController extends ApiActiveController
{
    public $modelClass = 'api\resources\EduSemestrSubjectCategoryTime';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new EduSemestrSubjectCategoryTime();

        $query = $model->find()
            ->andWhere([
//                'edu_semestr_id' => Yii::$app->request->get('edu_sem_id'),
//                'subject_id' => Yii::$app->request->get('sub_id'),
                'is_deleted' => 0
            ]);
//            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);



        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }
}
