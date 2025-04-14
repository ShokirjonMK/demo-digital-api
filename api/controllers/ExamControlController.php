<?php

namespace api\controllers;


use Yii;
use base\ResponseStatus;
use common\models\model\ExamControl;
use common\models\model\Faculty;
use common\models\model\Student;
use common\models\model\Subject;
use yii\db\Query;

class ExamControlController extends ApiActiveController
{
    public $modelClass = 'api\resources\ExamControl';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_control';
    public $controller_name = 'ExamControl';


    public function actionIndex($lang)
    {
        $model = new ExamControl();
        // $student = Student::findOne(['user_id' => Current_user_id()]);

        $query = $model->find();

        $query = $query->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $isArchived = Yii::$app->request->get('archived', 0);
        $query->andFilterWhere([$this->table_name . '.archived' => (int)$isArchived]);

        $onlyCurrent = (int)Yii::$app->request->get('current', 0);
        if ($onlyCurrent === 1) {
            $now = time();
            $query->andFilterWhere(['>=', $this->table_name . '.start', $now])
                ->andFilterWhere(['<=', $this->table_name . '.finish', $now]);
        }

        $statuses = json_decode(str_replace("'", "", Yii::$app->request->get('statuses')));
        if ($statuses) {
            $query->andFilterWhere([
                'in',
                $this->table_name . '.status',
                $statuses
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);
        // sort
        $query = $this->sort($query);
        // data

        // dd($query->createCommand()->rawSql);
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new ExamControl();
        $post = Yii::$app->request->post();
        // $post['duration'] =  strtotime($post['duration']);

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['duration2'])) {
            $post['duration2'] =  str_replace("'", "", $post['duration2']);
            $post['duration2'] =  str_replace('"', "", $post['duration2']);
            $duration2 = explode(":", $post['duration2']);
            $hours = isset($duration2[0]) ? $duration2[0] : 0;
            $min = isset($duration2[1]) ? $duration2[1] : 0;
            $post['duration2'] = (int)$hours * 3600 + (int)$min * 60;
        }

        $this->load($model, $post);
        if (isset($post['start'])) {
            $model['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish'] = strtotime($post['finish']);
        }
        if (isset($post['start2'])) {
            $model['start2'] = strtotime($post['start2']);
        }
        if (isset($post['finish2'])) {
            $model['finish2'] = strtotime($post['finish2']);
        }

        $result = ExamControl::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = ExamControl::findOne($id);

        // /*  is Self  */
        // $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
        // if ($t['status'] == 1) {
        //     if ($model->faculty_id != $t['UserAccess']->table_id) {
        //         return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        //     }
        // } elseif ($t['status'] == 2) {
        //     return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::FORBIDDEN);
        // }
        // /*  is Self  */

        $post = Yii::$app->request->post();


        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isset($post['duration'])) {
            $post['duration'] =  str_replace("'", "", $post['duration']);
            $post['duration'] =  str_replace('"', "", $post['duration']);
            $duration = explode(":", $post['duration']);
            $hours = isset($duration[0]) ? $duration[0] : 0;
            $min = isset($duration[1]) ? $duration[1] : 0;
            $post['duration'] = (int)$hours * 3600 + (int)$min * 60;
        }
        if (isset($post['duration2'])) {
            $post['duration2'] =  str_replace("'", "", $post['duration2']);
            $post['duration2'] =  str_replace('"', "", $post['duration2']);
            $duration2 = explode(":", $post['duration2']);
            $hours = isset($duration2[0]) ? $duration2[0] : 0;
            $min = isset($duration2[1]) ? $duration2[1] : 0;
            $post['duration2'] = (int)$hours * 3600 + (int)$min * 60;
        }

        if (isset($post['appeal2_at'])) {
            $post['appeal2_at'] = strtotime($post['appeal2_at']);
        }

        if (isset($post['appeal_at'])) {
            $post['appeal_at'] = strtotime($post['appeal_at']);
        }

        if (isset($post['status'])) {
            if ($model->status == 2 && $post['status'] == 2)
                unset($post['status']);
        }

        if (isset($post['status2'])) {
            if ($model->status2 == 2 && $post['status2'] == 2)
                unset($post['status2']);
        }

        $this->load($model, $post);
        if (isset($post['start'])) {
            $model['start'] = strtotime($post['start']);
        }
        if (isset($post['finish'])) {
            $model['finish'] = strtotime($post['finish']);
        }
        if (isset($post['start2'])) {
            $model['start2'] = strtotime($post['start2']);
        }
        if (isset($post['finish2'])) {
            $model['finish2'] = strtotime($post['finish2']);
        }


        $result = ExamControl::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamControl::find()
            ->andWhere([
                'id' => $id,
                'is_deleted' => 0
            ])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {

            if ($model->timeTable->teacher_user_id != current_user_id() && isRole('teacher')) {
                $errors[] = _e('This is not your timeTable');

                return $this->response(0, _e('There is an error occurred while processing.'), null, _e('This is not your timeTable'), ResponseStatus::BAD_REQUEST);
            }
            // Translate::deleteTranslate($this->table_name, $model->id);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
    public function actionNotCreate($lang)
    {
        $period = Yii::$app->request->get('period', 1);
        if ($period == 1) {
            $period = [1, 3, 5, 7];
        }
        if ($period == 2) {
            $period = [2, 4, 6, 8];
        }

        $query = (new Query())
            ->select([
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'subject_tr.name AS subject',
                'kafedra_tr.name AS kafedra',
                'COUNT(*) AS soni',
            ])
            ->from('time_table')
            ->leftJoin('exam_control', 'time_table.id = exam_control.time_table_id')
            ->leftJoin('profile', 'time_table.teacher_user_id = profile.user_id')
            ->leftJoin('translate AS subject_tr', [
                'AND',
                'subject_tr.model_id = time_table.subject_id',
                ['subject_tr.language' => 'uz'],
                ['subject_tr.table_name' => 'subject']
            ])
            ->leftJoin('subject', 'time_table.subject_id = subject.id')
            ->leftJoin('translate AS kafedra_tr', [
                'AND',
                'kafedra_tr.model_id = subject.kafedra_id',
                ['kafedra_tr.language' => 'uz'],
                ['kafedra_tr.table_name' => 'kafedra']
            ])
            ->where([
                'time_table.archived' => 0,
                'time_table.parent_id' => null,
            ])
            ->andWhere(['NOT', ['time_table.subject_category_id' => 1]])
            ->andWhere(['IN', 'time_table.semester_id', $period])
            ->andWhere(['exam_control.time_table_id' => null])
            ->groupBy([
                'profile.last_name',
                'profile.first_name',
                'profile.middle_name',
                'subject_tr.name',
                'kafedra_tr.name',
            ]);

        // Execute the query
        return $result = $query->all();
    }
}
