<?php

namespace api\controllers;

use common\models\model\Group;
use common\models\model\TeacherAccess;
use common\models\model\TimeTable1;
use common\models\model\TimeTableGroup;
use Yii;
use base\ResponseStatus;
use common\models\model\EduSemestr;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\StudentTimeTable;
use common\models\model\Subject;
use function GuzzleHttp\Promise\all;

class TimeTable1Controller extends ApiActiveController
{
    public $modelClass = 'api\resources\TimeTableCreate';

    public $table_name = 'time_table';

    public function actions()
    {
        return [];
    }


    public function actionIndex($lang)
    {
        $model = new TimeTable1();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);

        if (isRole('student')) {
            $student = Student::findOne(['user_id' => current_user_id()]);
            if ($student) {
                $query->andWhere(['in', 'edu_semestr_id', $student->activeSemestr ? $student->activeSemestr->id : null ]);
                $query->andWhere([
                    'group_id' => $student->group_id,
                    'group_type' => $student->type
                ]);
            }
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $query->andFilterWhere([
                'user_id' => current_user_id()
            ]);
        }

        $query = $this->filter($query, $model);

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);
        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionParentNull($lang)
    {
        $model = new TimeTable1();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0])
            ->andWhere(['parent_id' => null])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        $student = Student::findOne(['user_id' => current_user_id()]);

        if ($student) {

            // /** Kurs bo'yicha vaqt belgilash */
            // $errors = [];
            // if (!StudentTimeTable::chekTime()) {
            //     $errors[] = _e('This is not your time to choose!');
            //     return $this->response(0, _e('There is an error occurred while processing.'), null, $errors, ResponseStatus::UPROCESSABLE_ENTITY);
            // }
            // /** Kurs bo'yicha vaqt belgilash */

            $query->andWhere(['in', 'edu_semester_id', EduSemestr::find()->where(['edu_plan_id' => $student->edu_plan_id])->select('id')]);
            $query->andWhere(['language_id' => $student->edu_lang_id]);
        } else {

            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {

                $query->andFilterWhere([
                    'in', 'subject_id', Subject::find()->where([
                        'kafedra_id' => $k['UserAccess']->table_id
                    ])->select('id')
                ]);
            }
        }

        if (isRole('teacher') && !isRole('mudir')) {
            $query->andFilterWhere([
                'teacher_user_id' => current_user_id()
            ]);
        }

        if (isRole('mudir')) {
            $kafedra = Kafedra::findOne([
                'user_id' => current_user_id(),
                'status' => 1,
                'is_deleted' => 0,
            ]);
            if (isset($kafedra)) {
                $query->andFilterWhere([
                    'in', 'subject_id', Subject::find()->where([
                        'kafedra_id' => $kafedra->id
                    ])->select('id')
                ]);
            }
        }

        $kafedraId = Yii::$app->request->get('kafedra_id');
        if (isset($kafedraId)) {
            $query->andFilterWhere([
                'in', 'subject_id', Subject::find()->where([
                    'kafedra_id' => $kafedraId
                ])->select('id')
            ]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // dd($query->createCommand()->getRawSql());

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $post = Yii::$app->request->post();
        $result = TimeTable1::createItem($post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionCreateAddGroup($lang)
    {
        $post = Yii::$app->request->post();
        $result = TimeTable1::createAddGroup($post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = TimeTable1::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();

        $result = TimeTable1::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('TimeTable successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

    }

    public function actionView($lang, $id) {

        $model = new TimeTable1();
        $query = $model::findOne([
            'id' => $id,
            'status' => 1,
            'is_deleted' => 0,
        ]);

        if (!isset($query)) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        return $this->response(1, _e('Success.'), $query, null, ResponseStatus::OK);

    }

    public function actionViewww($lang, $id)
    {
        $view = [];
        $model = TimeTable1::find()
            ->andWhere(['id' => $id])
            ->one();
        if ($model->two_group == 1) {

        }
        $view['time-table'] = $model;
        if (isset($model)) {
            $dataPatok = [];
            $dataPatok[] = $model->group;
            if ($model->subject_category_id == TimeTable1::LECTURE)  {
                $patok = TimeTable1::find()->where([
                    'edu_plan_id' => $model->edu_plan_id,
                    'edu_semestr_id' => $model->edu_semestr_id,
                    'faculty_id' => $model->faculty_id,
                    'subject_id' => $model->subject_id,
                    'direction_id' => $model->direction_id,
                    'building_id' => $model->building_id,
                    'room_id' => $model->room_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'subject_category_id' => $model->subject_category_id,
                    'language_id' => $model->language_id,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                    ->andWhere(['!=' , 'id', $model->id])
                    ->all();
                foreach ($patok as $value) {
                    $dataPatok[] = $value->group;
                }
                $view['groups'] = $dataPatok;
            } else {
                $type = 1;
                if ($model->type == 1) {
                    $type = 2;
                } elseif ($model->type == 2) {
                    $type = 1;
                }
                $patok = TimeTable1::find()->where([
                    'group_id' => $model->group_id,
                    'edu_plan_id' => $model->edu_plan_id,
                    'edu_semestr_id' => $model->edu_semestr_id,
                    'faculty_id' => $model->faculty_id,
                    'subject_id' => $model->subject_id,
                    'direction_id' => $model->direction_id,
//                    'building_id' => $model->building_id,
                    'week_id' => $model->week_id,
                    'para_id' => $model->para_id,
                    'subject_category_id' => $model->subject_category_id,
                    'language_id' => $model->language_id,
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                    ->andWhere(['!=' , 'id', $model->id])
                    ->one();
                if (isset($patok)) {
                    $view['second-group'] = $patok;
                }
            }
        }
        if (count($view) == 0) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $view, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
//        return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        $model = TimeTable1::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
//        $result = TimeTable::findOne($id);

        if ($model) {
//            TimeTable::deleteAll(['parent_id' => $result->id]);
            if ($model->two_groups == 1) {
                $twoGroups = TimeTable1::find()->where([
                    'ids' => $model->ids,
                    'group_id' => $model->group_id,
//                    'edu_plan_id' => $model->edu_plan_id,
//                    'edu_year_id' => $model->edu_year_id,
//                    'edu_semestr_id' => $model->edu_semestr_id,
//                    'faculty_id' => $model->faculty_id,
//                    'subject_id' => $model->subject_id,
//                    'direction_id' => $model->direction_id,
//                    'week_id' => $model->week_id,
//                    'para_id' => $model->para_id,
//                    'subject_category_id' => 1,
//                    'language_id' => $model->language_id,
//                    'type' => $model->type,
//                    'two_groups' => $model->two_groups,
                    'is_deleted' => 0,
                    'status' => 1,
                ])
                    ->andWhere(['!=' , "id" , $model->id])
                    ->one();
                if (isset($twoGroups)) {
                    $twoGroups->is_deleted = 1;
                    $twoGroups->save(false);
                }
            }
            $model->is_deleted = 1;
            $model->save(false);

            return $this->response(1, _e('TimeTable and its children succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
