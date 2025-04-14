<?php

namespace api\controllers;

use common\models\model\StudentMark;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Profile;
use common\models\model\Translate;
use yii\db\Expression;

class StudentMarkController extends ApiActiveController
{
    public $modelClass = 'api\resources\StudentMark';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student_mark';
    public $controller_name = 'StudentMark';

    public function actionIndex($lang)
    {
        $model = new StudentMark();

        $query = $model->find()
            // ->andWhere([$table_name.'.status' => 1, $table_name . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.is_deleted' => 0])
            ->andWhere([$model->tableName() . '.archived' => 0])
            ->join('INNER JOIN', 'student', 'student.id = ' . $model->tableName() . '.student_id')
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id');


        if (isRole("student")) {
            $query = $query->andWhere([
                'student_id' => $this->student()
            ]);
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


        /**Baho bo'yicha */
        $mark = Yii::$app->request->get('mark');
        if (isset($mark)) {
            if ($mark == 0) {
                $query->andFilterWhere([$model->tableName() . '.ball' => null]);
            } elseif ($mark == 2) {
                // ball between 0 and 55
                $query->andFilterWhere(['between', $model->tableName() . '.ball', 0, 55]);
            } elseif ($mark == 3) {
                // ball between 56 and 71
                $query->andFilterWhere(['between', $model->tableName() . '.ball', 56, 71]);
            } elseif ($mark == 4) {
                // ball between 72 and 85
                $query->andFilterWhere(['between', $model->tableName() . '.ball', 72, 85]);
            } elseif ($mark == 5) {
                // ball greater than 86
                $query->andFilterWhere(['>', $model->tableName() . '.ball', 85]);
            }
        }

        /** Yiqilganlar */
        $fallen = Yii::$app->request->get('fallen');
        if (isset($fallen)) {
            // ball is null or smaller than 56
            $query->andWhere(['or', [$model->tableName() . '.ball' => null], ['<', $model->tableName() . '.ball', 56]]);
        }

        /** qarzdorligi yoq */
        $no_debt = Yii::$app->request->get('no_debt');
        if (isset($no_debt)) {
            // ball greater than or equal to 56
            $query->andWhere(['>=', $model->tableName() . '.ball', 56]);
        }

        /** qarzdorligi yoq */
        $no_debt = Yii::$app->request->get('no_debt');
        if (isset($no_debt)) {
            // ball greater than or equal to 56
            $query->andWhere(['>=', $model->tableName() . '.ball', 56]);
        }


        /** 3 tadan kam qarzdorlik borlar */
        $lass_3_debts = Yii::$app->request->get('lass_3_debts');
        if (isset($lass_3_debts)) {
            if ($lass_3_debts == 1)
                $query->andWhere(['or', [$model->tableName() . '.ball' => null], ['<', $model->tableName() . '.ball', 56]])
                    ->groupBy($model->tableName() . 'student_id')
                    ->having(new Expression('COUNT(*) <= 3'));
        }

        /** 5 tadan ko'p qarzdorlik borlar */
        $more_5_debts = Yii::$app->request->get('more_5_debts');
        if (isset($more_5_debts)) {
            if ($more_5_debts == 1)
                $query->andWhere(['or', [$model->tableName() . '.ball' => null], ['<', $model->tableName() . '.ball', 56]])
                    ->groupBy($model->tableName() . 'student_id')
                    ->having(new Expression('COUNT(*) >= 5'));
        }

        // faculty_id
        if (isRole("dean")) {
            $t = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query = $query->andWhere([
                    $model->tableName() . '.faculty_id' => $t['UserAccess']->table_id
                ]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([
                    $model->tableName() . '.faculty_id' => -1
                ]);
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        // $query = $this->sort($query);
        $query->orderBy(['order' => SORT_ASC]);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        // return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        $model = new StudentMark();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = StudentMark::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        $model = StudentMark::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = StudentMark::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    // public function actionChangeBallWithFile($lang, $id = null)
    // {
    //     $model = ExamControlStudent::findOne($id);
    //     $post = Yii::$app->request->post();

    //     if (!$model) {
    //         $model = new ExamControlStudent();
    //         $this->load($model, $post);
    //         $result = ExamControlStudent::createItem($model, $post, 1);

    //         if (!is_array($result)) {
    //             return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
    //         } else {
    //             return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    //         }
    //     }


    //     $result = ExamControlStudent::changeBallWithFile($model, $post);

    //     if (!is_array($result)) {
    //         return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
    //     } else {
    //         return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
    //     }
    // }

    public function actionView($lang, $id)
    {
        $model = StudentMark::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        $model = StudentMark::find()
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
}
