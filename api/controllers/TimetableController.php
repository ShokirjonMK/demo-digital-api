<?php

namespace api\controllers;

use api\resources\User;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\model\Department;
use common\models\model\EduPlan;
use common\models\model\Faculty;
use common\models\model\Group;
use common\models\model\Profile;
use common\models\model\SubjectTopic;
use common\models\model\TeacherAccess;
use common\models\model\Timetable;
use common\models\model\TimeTable1;
use common\models\model\TimetableDate;
use common\models\model\TimeTableGroup;
use common\models\model\TimetableIds;
use common\models\model\UserAccess;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use yii\db\Expression;

class TimetableController extends ApiActiveController
{
    public $modelClass = 'common\models\model\Timetable';

    public $table_name = 'timetable';

    public $controller_name = 'Timetable';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Timetable();

        $query = $model->find()->where(['is_deleted' => 0]);

        $startDate = date('Y-m-d' , strtotime(Yii::$app->request->get('start_date') . ' -1 day'));
        $endDate = date('Y-m-d' , strtotime(Yii::$app->request->get('end_date') . ' +1 day'));

        $startDate = ($startDate !== null) ? date('Y-m-d', strtotime("$startDate -1 day")) : null;
        $endDate = ($endDate !== null) ? date('Y-m-d', strtotime("$endDate +1 day")) : null;

        $subquery = TimetableDate::find()
            ->select('timetable_id')
            ->where(['between', 'date', new Expression('DATE(:start_date)'), new Expression('DATE(:end_date)')])
            ->params([':start_date' => $startDate, ':end_date' => $endDate]);

        $query->andWhere(['in' , 'id' , $subquery]);

        $query = $this->filter($query, $model);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }


    public function actionUser($lang)
    {
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andWhere(['users.deleted' => 0])
            ->groupBy('profile.user_id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

        $eduYearId = Yii::$app->request->get('edu_year_id');
        $date = Yii::$app->request->get('date');
        $type = Yii::$app->request->get('type');

        $subModel = new TimetableDate();
        $subQuery = $subModel->find()
            ->select('user_id')
            ->where([
                'edu_year_id'  => $eduYearId,
                'status' => 1,
                'is_deleted' => 0
            ]);

        if (isset($type) && $type == 1) {
            $subQuery->andWhere(['<' , 'date' , $date]);
        } elseif (isset($type) && $type == 2) {
            $subQuery->andWhere(['=' , 'date' , $date]);
        }

        $query->andWhere(['in' , 'users.id' , TimetableDate::find()
            ->select('user_id')
            ->where([
                'edu_year_id'  => Yii::$app->request->get('edu_year_id'),
                'status' => 1,
                'is_deleted' => 0
            ])
        ]);

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $kafedraId,
                    'user_access_type_id' => Kafedra::USER_ACCESS_TYPE_ID,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
            ]);
        }

        $facultyId = Yii::$app->request->get('faculty_id');
        if (isset($facultyId)) {
            $query->andFilterWhere([
                'in', 'users.id', UserAccess::find()->select('user_id')->where([
                    'table_id' => $facultyId,
                    'user_access_type_id' => Faculty::USER_ACCESS_TYPE_ID,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
            ]);
        }

        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        //  Filter from Profile
        $profile = new Profile();
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }


    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();

        $resultIds = TimetableIds::createItem();
        if (!$resultIds['is_ok']) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $resultIds['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }

        $ids = $resultIds['ids'];
        $result = Timetable::createItem($post , $ids);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionAddGroup($lang)
    {
        $post = Yii::$app->request->post();
        $result = Timetable::addGroup($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAddDay($lang, $id)
    {
        $post = Yii::$app->request->post();
        $models = Timetable::find()
            ->where(['ids' => $id,'status' => 1, 'is_deleted' => 0])
            ->all();

        if (count($models) == 0) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Timetable::addDay($models , $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();

        $result = Timetable::updateItem($id, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($lang, $id) {

        $model = new Timetable();
        $query = $model->findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0,
        ]);

        if (!isset($query)) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $query, null, ResponseStatus::OK);

    }

    public function actionDelete($lang, $id)
    {
        $models = Timetable::find()
            ->where([
                'ids' => $id,
                'is_deleted' => 0
            ])->all();
        if (count($models) == 0) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Timetable::deleteItem($models);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully removed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDeleteOne($lang, $id)
    {
        $model = Timetable::findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = Timetable::deleteItemOne($model);

        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully removed.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionEduPlan()
    {
        $model = new EduPlan();

        $query = $model->find()->where(['status' => 1 , 'is_deleted' => 0])->orderBy('edu_year_id desc');

        if (isRole('teacher')) {
            $userId = current_user_id();
        } else {
            $userId = Yii::$app->request->get('user_id');
        }

        $query->andWhere(['in', 'id',
            TimetableDate::find()
                ->select('edu_plan_id')
                ->where([
                    'user_id' => $userId,
                    'status' => 1,
                    'is_deleted' => 0
                ])
        ]);

        $query = $this->filter($query, $model);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionEduSemestr($lang, $id)
    {
        $model = new EduSemestr();

        $query = $model->find()->where(['status' => 1 , 'is_deleted' => 0])->orderBy('semestr_id asc');

        if (isRole('teacher')) {
            $userId = current_user_id();
        } else {
            $userId = Yii::$app->request->get('user_id');
        }

        $query->andWhere(['in', 'id',
            TimetableDate::find()
                ->select('edu_semestr_id')
                ->where([
                    'user_id' => $userId,
                    'edu_plan_id' => $id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
        ]);

        $query = $this->filter($query, $model);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionAttend($lang)
    {
        $model = new Timetable();

        $query = $model->find()
            ->where(['is_deleted' => 0]);

        $startDate = date('Y-m-d' , strtotime(Yii::$app->request->get('start_date') . ' -1 day'));
        $endDate = date('Y-m-d' , strtotime(Yii::$app->request->get('end_date') . ' +1 day'));

        $startDate = ($startDate !== null) ? date('Y-m-d', strtotime("$startDate -1 day")) : null;
        $endDate = ($endDate !== null) ? date('Y-m-d', strtotime("$endDate +1 day")) : null;

        $subquery = TimetableDate::find()
            ->select('timetable_id')
            ->where(['between', 'date', new Expression('DATE(:start_date)'), new Expression('DATE(:end_date)')])
            ->params([':start_date' => $startDate, ':end_date' => $endDate]);

        $query->andWhere(['in' , 'id' , $subquery]);

        $query = $this->filter($query, $model);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionStudentType($lang)
    {
        $post = Yii::$app->request->post();
        $result = Timetable::studentType($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionTeacherTopic()
    {
        $model = new TimetableDate();
        $userId = isRole('teacher') ? current_user_id() : Yii::$app->request->get('user_id');

        $subjectId = Yii::$app->request->get('subject_id');
        $subjectCategoryId = Yii::$app->request->get('subject_category_id');
        $groupId = Yii::$app->request->get('group_id');

        $data = $model->find()
            ->where(['is_deleted' => 0, 'status' => 1])
            ->andFilterWhere(['user_id' => $userId])
            ->andFilterWhere(['group_id' => $groupId])
            ->andFilterWhere(['subject_id' => $subjectId])
            ->andFilterWhere(['subject_category_id' => $subjectCategoryId])
            ->orderBy(['date' => SORT_ASC, 'para_id' => SORT_ASC])
            ->asArray()
            ->all();

        $data = array_map(function ($item) {
            return [
                'date' => $item['date'],
                'para' => $item['para_id'],
                'subject' => $item['subject_id']
            ];
        }, $data);

        $topics = SubjectTopic::find()
            ->where([
                'subject_id' => $subjectId,
                'subject_category_id' => $subjectCategoryId,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->orderBy(['order' => SORT_ASC])
            ->asArray()
            ->all();

        $t = [];
        foreach ($topics as $topic) {
            $hour = $topic['hours'];
            while ($hour >= 2) {
                $t[] = ['id' => $topic['id'], 'hour' => 2];
                $hour -= 2;
            }
        }

        $merged = [];
        $maxLength = max(count($data), count($t));

        for ($i = 0; $i < $maxLength; $i++) {
            $merged[] = [
                'date' => $data[$i]['date'] ?? null,
                'para' => $data[$i]['para'] ?? null,
                'subject' => $data[$i]['subject'] ?? null,
                'topic_id' => $t[$i]['id'] ?? null,
                'hours' => $t[$i]['hour'] ?? null
            ];
        }


        dd($merged);
    }

}
