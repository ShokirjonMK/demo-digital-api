<?php

namespace api\controllers;

use api\resources\User;
use common\models\model\Vocation;
use Yii;
use base\ResponseStatus;
use common\models\model\Profile;
use common\models\model\Translate;
use common\models\model\UserAccess;

class VocationController extends ApiActiveController
{
    public $modelClass = 'api\resources\Vocation';

    public function actions()
    {
        return [];
    }

    public $table_name = 'vocation';
    public $controller_name = 'Vocation';

    public function actionIndex($lang)
    {
        // Instantiate necessary models
        $model = new Vocation();
        $profile = new Profile();
        $user = new User();
        $userAccess = new UserAccess();

        // Get filter parameters once for reuse
        $query = $model->find()->with(['infoRelation'])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->innerJoin('profile', 'profile.user_id = ' . $this->table_name . '.user_id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $query->join('LEFT JOIN', 'user_access', 'user_access.user_id = ' . $this->table_name . '.user_id');

        // $query->andWhere([
        //     'user_access.is_deleted' => 0,
        //     'user_access.archived' => 0,
        // ]);

        // Date filters
        $fromDate = Yii::$app->request->get('from_date');
        $toDate = Yii::$app->request->get('to_date');
        if ($fromDate) {
            $query->andFilterWhere(['>=', $this->table_name . '.start_date', $fromDate]);
        }
        if ($toDate) {
            $query->andFilterWhere(['<=', $this->table_name . '.finish_date', $toDate]);
        }

        // Profile and User filters
        $filter = Yii::$app->request->get('filter');
        if ($filter) {
            $filter = json_decode(str_replace("'", "", $filter), true);
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $profile->attributes())) {
                    $query->andFilterWhere([$profile->tableName() . '.' . $attribute => $id]);
                } elseif (in_array($attribute, $user->attributes())) {
                    $query->andFilterWhere([$user->tableName() . '.' . $attribute => $id]);
                } elseif (in_array($attribute, $userAccess->attributes())) {
                    $query->andFilterWhere([$userAccess->tableName() . '.' . $attribute => $id]);
                }
            }
        }

        // Filter-like query
        $queryfilter = Yii::$app->request->get('filter-like');
        if ($queryfilter) {
            $queryfilter = json_decode(str_replace("'", "", $queryfilter), true);
            foreach ($queryfilter as $attributeq => $word) {
                $likeOperator = '%' . $word . '%';
                if (in_array($attributeq, $profile->attributes())) {
                    $query->andFilterWhere(['like', $profile->tableName() . '.' . $attributeq, $likeOperator, false]);
                } elseif (in_array($attributeq, $user->attributes())) {
                    $query->andFilterWhere(['like', $user->tableName() . '.' . $attributeq, $likeOperator, false]);
                } elseif (in_array($attributeq, $userAccess->attributes())) {
                    $query->andFilterWhere(['like', $userAccess->tableName() . '.' . $attributeq, $likeOperator, false]);
                }
            }
        }

        // Additional filters (e.g., from the method)
        $query = $this->filterAll($query, $model);

        // Sorting
        $query = $this->sort($query);

        // Fetch data
        $data = $this->getData($query);

        // Return response
        return $this->response(1, _e('Success'), $data);
    }

    // public function actionIndexas($lang)
    // {
    //     $model = new Vocation();

    //     $query = $model->find()
    //         ->with(['infoRelation'])
    //         // ->andWhere([$this->table_name . '.is_deleted' => 0])
    //         ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
    //         ->innerJoin('profile', 'profile.user_id = ' . $this->table_name . '.user_id')
    //         ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

    //     if (isset(Yii::$app->request->get('from_date'))) {
    //         $query = $query->andFilterWhere(['>=', $this->table_name . '.start_date', Yii::$app->request->get('from_date')]);
    //     }
    //     if (isset(Yii::$app->request->get('to_date'))) {
    //         $query = $query->andFilterWhere(['<=', $this->table_name . '.end_date', Yii::$app->request->get('to_date')]);
    //     }


    //     //  Filter from Profile 
    //     $profile = new Profile();
    //     $user = new User();
    //     $filter = Yii::$app->request->get('filter');
    //     $filter = json_decode(str_replace("'", "", $filter));
    //     //  Filter from Profile 

    //     if (isset($filter)) {
    //         foreach ($filter as $attribute => $id) {
    //             if (in_array($attribute, $profile->attributes())) {
    //                 $query = $query->andFilterWhere([$profile->tableName() . '.' . $attribute => $id]);
    //             }
    //             if (in_array($attribute, $user->attributes())) {
    //                 $query = $query->andFilterWhere([$user->tableName() . '.' . $attribute => $id]);
    //             }
    //         }
    //     }

    //     $queryfilter = Yii::$app->request->get('filter-like');
    //     $queryfilter = json_decode(str_replace("'", "", $queryfilter));
    //     if (isset($queryfilter)) {
    //         foreach ($queryfilter as $attributeq => $word) {
    //             if (in_array($attributeq, $profile->attributes())) {
    //                 $query = $query->andFilterWhere(['like', $profile->tableName() . '.' . $attributeq, '%' . $word . '%', false]);
    //             }
    //             if (in_array($attributeq, $user->attributes())) {
    //                 $query = $query->andFilterWhere(['like', $user->tableName() . '.' . $attributeq, '%' . $word . '%', false]);
    //             }
    //         }
    //     }

    //     // filter
    //     $query = $this->filterAll($query, $model);

    //     // sort
    //     $query = $this->sort($query);

    //     // data
    //     $data =  $this->getData($query);
    //     return $this->response(1, _e('Success'), $data);
    // }

    public function actionCreate($lang)
    {
        $model = new Vocation();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = Vocation::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Vocation::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $post['year'] = date('Y', strtotime($post['start_date'] ?? time()));
        $post['month'] = date('m', strtotime($post['start_date'] ?? time()));
        $this->load($model, $post);
        $result = Vocation::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Vocation::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Vocation::find()
            ->andWhere(['id' => $id])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            Translate::deleteTranslate($this->table_name, $model->id);
            $model->delete();

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
