<?php

namespace api\controllers;


use common\models\model\StudentTopicPermission;
use common\models\model\SubjectTopic;
use Yii;
use base\ResponseStatus;


class SubjectTopicController extends ApiActiveController
{

    public $modelClass = 'api\resources\SubjectTopic';

    public function actions()
    {
        return [];
    }

    public $table_name = 'subject_topic';

    public $controller_name = 'SubjectTopic';

    public function actionIndex($lang)
    {
        $model = new SubjectTopic();

        // return $this->teacher_access(1, ['lang_id']);

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->orderBy('order asc')
            ->andFilterWhere(['lang_id' => Yii::$app->request->get('lang_id')])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')])
            ->andFilterWhere(['subject_category_id' => Yii::$app->request->get('category')])
            ->andFilterWhere(['subject_id' => Yii::$app->request->get('sub_id')]);

//        if ((isRole('teacher')  && !isRole('mudir')) && (isRole('teacher') && !isRole('contenter'))) {
//            $query->andWhere(['in', 'lang_id', $this->teacher_access(1, ['language_id'])]);
//        }

        if (isRole('teacher')) {
            $query->andFilterWhere(['in', 'lang_id', $this->teacher_access(1, ['language_id'])]);
        }
        if (isRole('student')) {
            $query->andWhere(['lang_id' => $this->student(2) ?? $this->student(2)->edu_lang_id]);
        }

        // dd($query->createCommand()->getSql());

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new SubjectTopic();
        $post = Yii::$app->request->post();

        $this->load($model, $post);

        $result = SubjectTopic::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionExport($lang) {
        $post = Yii::$app->request->post();
        $result = SubjectTopic::createExport($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = SubjectTopic::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();
        $modelOrder = $model->order;
        $this->load($model, $post);
        $result = SubjectTopic::updateItem($model, $post , $modelOrder);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionOrder($lang , $id) {
        $model = SubjectTopic::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = SubjectTopic::updateOrder($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = SubjectTopic::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

//        if (isRole('student')) {
//            $studentPermission = new StudentTopicPermission();
//            $permission = $studentPermission->find()->where([
//                'user_id' => current_user_id(),
//                'is_deleted' => 0
//            ]);
//            if ($model->subject_category_id == 1) {
//                $permission->andWhere([
//                    'topic_id' => $model->id,
//                ]);
//            } else {
//                $permission->andWhere([
//                    'topic_id' => $model->parent_id,
//                ]);
//            }
//            $permission->one();
//            if (!isset($permission)) {
//                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
//            }
//        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = SubjectTopic::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        if ($model) {
            $model->is_deleted = 1;
            if ($model->save(false)) {
                $order = SubjectTopic::find()
                    ->where(['>' , 'order', $model->order])
                    ->andWhere([
                        'subject_id'=> $model->subject_id,
                        'subject_category_id'=> $model->subject_category_id,
                        'is_deleted' => 0
                    ])
                    ->all();
                if (count($order) > 0) {
                    foreach ($order as $order_item) {
                        $order_item->order = $order_item->order - 1;
                        $order_item->save(false);
                    }
                }
            }

            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}
