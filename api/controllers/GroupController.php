<?php

namespace api\controllers;

use api\resources\AccessControl;
use api\resources\User;
use common\models\model\EduSemestr;
use common\models\model\EduYear;
use common\models\model\Group;
use common\models\model\Student;
use common\models\model\TimeTable1;
use common\models\model\TimetableDate;
use Yii;
use base\ResponseStatus;
use yii\web\ForbiddenHttpException;

use common\models\model\Faculty;
use common\models\model\Translate;
use common\models\model\UserAccess;

use function PHPSTORM_META\type;

class GroupController extends ApiController
{
    public function actions()
    {
        return [];
    }

    public $table_name = 'group';
    public $controller_name = 'Group';

//    const USER_ACCESS_TYPE_ID = 1;
    const ROLE = 'dean';

    public function actionIndex($lang)
    {
        $model = new Group();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
//            ->with(['infoRelation'])
//            ->andWhere([$this->table_name . '.is_deleted' => 0])
//            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
//            ->groupBy($this->table_name . '.id')
//            ->andWhere(['tr.language' => Yii::$app->request->get('lang')])
            ->andFilterWhere(['like', 'unical_name', Yii::$app->request->get('query')]);


//        $eduYearId = EduYear::activeEduYear();
        $eduSemestr = new EduSemestr();
        $eduSemestrAll = $eduSemestr->find()
            ->select('edu_plan_id')
            ->where([
//                'edu_year_id' => $eduYearId->id,
//                'status' => 1,
                'is_deleted' => 0,
            ]);

        $eduSemestrAll = $this->filterAnotherTable($eduSemestrAll, $eduSemestr);
        $eduSemestrAll->asArray()->all();
        $query->andWhere(['in', 'edu_plan_id' , $eduSemestrAll]);

        // teacher roli bilan kirganda faqat o'zini guruhlarini olish
        if (isRole('teacher')) {
            $timeTable = TimetableDate::find()
                ->select('group_id')
                ->where([
                    'user_id' => current_user_id(),
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->andFilterWhere(['edu_year_id' => activeYearId()]);
            $query->andWhere(['in', 'id' , $timeTable]);
        } elseif (isRole('tutor')) {
            $query->andFilterWhere(['in' , 'id' , Student::find()
                ->select('group_id')
                ->where(['is_deleted' => 0, 'tutor_id' => current_user_id()])
                ->groupBy('group_id')]);
        } else {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    'faculty_id' => $t['UserAccess']
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    'faculty_id' => -1
                ]);
            }
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
        $model = new Group();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Group::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Group::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        /* if ($this->checkLead($model, self::ROLE)) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        } */

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Group::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Group::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Group::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionGroup()
    {
        $model = new Group();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andFilterWhere(['like', 'unical_name', Yii::$app->request->get('query')]);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

}
