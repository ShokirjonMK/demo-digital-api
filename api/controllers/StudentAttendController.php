<?php

namespace api\controllers;

use common\models\model\StudentAttend;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\Student;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class StudentAttendController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentAttend';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_attend';
    public $controller_name = 'StudentAttend';

    public function actionIndex($lang)
    {
        $model = new StudentAttend();

        $query = $model->find()
            // ->with(['infoRelation'])
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.archived' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            // ->join("INNER JOIN", "translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'" )
        ;

        $student = Student::findOne(['user_id' => current_user_id()]);
        if ($student && isRole('student')) {
            $query->andWhere([$model->tableName() . '.student_id' => $student->id]);
        }

        //  Filter from Profile 
        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $id]);
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
        // ***

        //  Filter from student 
        $student = new Student();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $student->attributes())) {
                    $query = $query->andFilterWhere([$student->tableName() . '.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $student->attributes())) {
                    $query = $query->andFilterWhere(['like', $student->tableName() . '.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        // ***


        /*  $group_by = Yii::$app->request->get('group_by');
        if (isset($group_by)) {
            if (($group_by[0] == "'") && ($group_by[strlen($group_by) - 1] == "'")) {
                $group_by =  substr($group_by, 1, -1);
            }
            // $query = $query->select([$this->table_name . '.*', 'COUNT(' . $this->table_name . '.id) AS countlike']);
            // ->join('LEFT JOIN', Likes::tableName(), 'videos.id=likes.video_id')
            // ->groupBy('videos.id')
            // ->limit(10);

            $query = $query->groupBy(((array)json_decode($group_by)));
            // $query = $query->orderBy(['countlike' => SORT_DESC]);
            $query = $query->orderBy(['COUNT(' . $this->table_name . '.id)' => SORT_DESC]);
        } */

        // filter
        $query = $this->filterAll($query, $model);

        // add order
        $query = $query->orderBy(['date' => SORT_ASC]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionByDate($date = null)
    {
        // Format the date and get the week number
        $formattedDate = Yii::$app->request->get('date') ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $weekNumber = date('N', strtotime($formattedDate)); // 1 (Monday) to 7 (Sunday)

        // Check if 'not_coming' is set in the request
        if (null !== Yii::$app->request->get('not_coming')) {
            $model = new Student();
            $query = $model->find()
                // ->select('student.id')
                ->innerJoin('student_time_table', 'student_time_table.student_id = student.id')
                ->where(['student_time_table.week_id' => $weekNumber])
                ->andWhere(['student_time_table.archived' => 0])
                ->andWhere(['student.status' => 10])
                ->andWhere(['student.is_deleted' => 0])
                ->groupBy('student.id')
                ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0)', [':date' => $formattedDate])
                // ->all()
            ;
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // sqlraw($query);
            // data
            $data =  $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (null !== Yii::$app->request->get('not')) {
            $model = new Student();
            $query = $model->find()
                // ->select('student.id')
                ->innerJoin('student_attend', 'student_attend.student_id = student.id')
                ->where(['student_attend.date' => $formattedDate])
                ->andWhere(['student.status' => 10])
                ->andWhere(['student.is_deleted' => 0])
                // ->andWhere(['student_time_table.archived' => 0])
                ->groupBy('student.id')
                // ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0)', [':date' => $formattedDate])
                // ->all()
            ;
            $query = $this->filterAll($query, $model);
            // sort
            $query = $this->sort($query);
            // sqlraw($query);
            // data
            $data =  $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        $query = (new \yii\db\Query())
            ->select([
                'faculty_id',
                'COUNT(DISTINCT student_id) AS student_count'
            ])
            ->from('student_attend')
            ->where(['date' => $date])
            ->groupBy('faculty_id');
        $result = $query->all();
        // dd($result);
        return $this->response(1, ('Success.'), $result, null, ResponseStatus::OK);
        // return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionCreate($lang)
    {
        $model = new StudentAttend();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentAttend::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = StudentAttend::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = StudentAttend::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView111($lang, $id)
    {

        $faculties = Faculty::find()->andWhere(['is_deleted' => 0])->all();

        foreach ($faculties as $faculty) {
            for ($i = 1; $i <= 26; $i++) {

                if ($i <= 9) {
                    $i = "0" . $i;
                }
                $date =  "2023-05-" . $i;

                $model = StudentAttend::find()
                    ->andWhere(['date' => $date])
                    ->andWhere(['faculty_id' => $faculty->id])
                    ->groupBy('student_id')
                    ->count();
                $data[] = ($faculty->id . "--" . $date . "--" . $model);
                $databydate[$date] = $model;
            }

            $umdataa[$faculty->id] = $databydate;
        }
        return $umdataa;
        die;

        $model = StudentAttend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionView($lang, $id)
    {
        $model = StudentAttend::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = StudentAttend::find()
            ->where(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('tutor')) {
            if ($model->created_by != 0) {
                return $this->response(0, _e('You can only delete turniket data.'), null, null, ResponseStatus::FORBIDDEN);
            }

            $model->status = 3;
        }

        $model->is_deleted = 1;

        if ($model->save()) {
            return $this->response(1, _e($this->controller_name . ' successfully removed.'), null, null, ResponseStatus::OK);
        }

        return $this->response(0, _e('An error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
