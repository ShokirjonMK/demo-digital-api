<?php

namespace api\controllers;

use common\models\model\Subject;
use Yii;
use base\ResponseStatus;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\TeacherAccess;

class SubjectController extends ApiActiveController
{
    public $modelClass = 'api\resources\Subject';

    public function actions()
    {
        return [];
    }

    public $table_name = 'subject';
    public $controller_name = 'Subject';

    public function actionIndex($lang)
    {
        $model = new Subject();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere([$this->table_name . '.is_deleted' => 0])
            ->leftJoin("translate tr", "tr.model_id = $this->table_name.id and tr.table_name = '$this->table_name'")
            ->groupBy($this->table_name . '.id')
            ->andFilterWhere(['like', 'tr.name', Yii::$app->request->get('query')]);

        $period = Yii::$app->request->get('period');
        if ($period) {
            if ($period == 1) {
                $period = [1, 3, 5, 7];
            }
            if ($period == 2) {
                $period = [2, 4, 6, 8];
            }
            $query = $query->andWhere(['in', $this->table_name . '.semestr_id', $period]);
        }
        $facultyId = Yii::$app->request->get('faculty_id');
        if ($facultyId) {
            $query = $query->andWhere(['in', 'kafedra_id', Kafedra::find()
                ->where(['faculty_id' => $facultyId])
                ->select('id')]);
        }

        if (isRole('content_assign') || isRole('edu_quality') || isRole('otv')) {

            // filter
            $query = $this->filterAll($query, $model);

            // sort
            $query = $this->sort($query);

            // data
            $data =  $this->getData($query);
            return $this->response(1, _e('Success'), $data);
        }

        if (isRole("dean")) {
            $k = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {
                $query->andFilterWhere(['in', 'kafedra_id', Kafedra::find()->where(['faculty_id' => $k['UserAccess']->table_id])->select('id')]);
            }
        } elseif (isRole('mudir') || isRole('mudir_vise')) {
            $k = $this->isSelf(Kafedra::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {
                // $kafedraIds = Kafedra::find()->where(['faculty_id' => $t['UserAccess']->table_id])->select('id');
                // $query->andFilterWhere(['in', 'kafedra_id', $kafedraIds]);
                $query->andFilterWhere([
                    'kafedra_id' => $k['UserAccess']->table_id
                ]);
            } elseif ($k['status'] == 2) {
                $query->andFilterWhere([
                    'kafedra_id' => -1
                ]);
            }
        } elseif (isRole("teacher")) {
            $teacherAccessSubjectIds = TeacherAccess::find()
                ->select('subject_id')
                ->where(['user_id' => current_user_id(), 'is_deleted' => 0])
                ->groupBy('subject_id');

            if ($teacherAccessSubjectIds) {
                $query->andFilterWhere(['in', $this->table_name . '.id', $teacherAccessSubjectIds]);
            } else {
                $query->andFilterWhere(['kafedra_id' => -1]);
            }
        } else {
            /*  is Self  */
            $k = $this->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($k['status'] == 1) {
                $query->andFilterWhere(['in', 'kafedra_id', Kafedra::find()->where(['faculty_id' => $k['UserAccess']->table_id])->select('id')]);
                // $query->andFilterWhere([
                //     'kafedra_id' => $k['UserAccess']->table_id
                // ]);
            } elseif ($k['status'] == 2) {
                $query->andFilterWhere([
                    'kafedra_id' => -1
                ]);
            }
            /*  is Self  */
        }
        // if (Yii::$app->request->get('per-page') == 0) {
        //     $query->andFilterWhere(['in', $model->tableName() . '.id', [45, 47, 630, 55, 46, 53, 234, 50, 332, 439, 438, 442, 654, 514, 632, 558, 441, 306, 68, 508, 436, 437, 582, 152, 537, 243, 337, 340, 395, 191, 418, 308, 102, 103, 171, 414, 623, 468, 49, 615, 481, 661, 163, 56, 386, 469, 655, 112, 38, 660, 39, 488, 663, 659, 210, 230, 205, 232, 150, 506, 640, 144, 18, 146, 11, 12, 15, 465, 21, 25, 23, 26, 653, 652, 651, 621, 235, 513, 584, 556, 575, 444, 657, 566, 567, 312, 240, 2, 207, 241, 242, 137, 389, 448, 449, 358, 117, 142, 403, 404, 378, 119, 385, 122, 238, 247, 52, 631, 57, 412, 495, 413, 493, 299, 509, 561, 129, 597, 252, 471, 60, 656, 626, 557, 256, 639, 367, 635, 73, 229, 75, 634, 87, 84, 649, 447, 648, 264, 369, 642, 643, 644, 645, 646, 647, 343, 77, 86, 650, 424, 637, 638, 428, 180, 430, 431, 429, 211, 555, 434, 432, 662, 665, 664, 179, 209, 185, 172, 224, 85, 221, 315, 217, 218]]);
        // }

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
        $model = new Subject();
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = Subject::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Subject::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Subject::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Subject::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Subject::find()
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
