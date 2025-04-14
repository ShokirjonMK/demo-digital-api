<?php

namespace api\controllers;

use Yii;
use api\resources\Reference;
use base\ResponseStatus;
use common\models\ReferenceInfo;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;

class ReferenceController extends ApiActiveController
{
    public $modelClass = 'api\resources\Reference';
    public $type; // Child class larda shu attribute override qilinishi kerak.
    public $typeName;

    public function init(){
        
        parent::init();
        if ($this->type === null) {
            throw new InvalidConfigException('The "type" property must be set.');
        }

        $this->typeName = Inflector::titleize(Inflector::id2camel($this->type));
    }

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Reference();

        $query = $model->find()
            ->with(['infoRelation'])
            ->andWhere(['type' => $this->type,  'deleted' => 0])
            // ->andWhere(['type' => $this->type, 'status' => 1, 'deleted' => 0])
            ->join('INNER JOIN', 'reference_info info', 'info.reference_id = reference.id')
            ->andWhere(['language' => $lang])
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

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
        $model = new Reference();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $model->type = $this->type;
        $result = Reference::createItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e($this->typeName . ' successfully created.'), $model, null, ResponseStatus::CREATED);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionUpdate($lang,$id)
    {
        $model = Reference::findOne($id);

        if(!$model || $model->type !== $this->type){
            return $this->response(0, _e($this->typeName . ' not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $model->type = $this->type;
        $result = Reference::updateItem($model, $post);
        if(!is_array($result)){
            return $this->response(1, _e($this->typeName . ' successfully updated.'), $model, null, ResponseStatus::OK);     
        }else{
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);     
        }
    }

    public function actionView($lang, $id)
    {
        $model = Reference::find()
                ->with(['infoRelation'])
                ->join('INNER JOIN', 'reference_info info', 'info.reference_id = reference.id')
                ->andWhere(['id' => $id, 'language' => $lang])
                ->one();
        if(!$model || $model->type !== $this->type){
            return $this->response(0, _e($this->typeName . ' not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Reference::findOne($id);
        if(!$model || $model->type !== $this->type){
            return $this->response(0, _e($this->typeName . ' not found.'), null, null, ResponseStatus::NOT_FOUND);     
        }

        // remove translations 
        ReferenceInfo::deleteAll(['reference_id' => $id]);

        // remove model
        $result = Reference::findOne($id)->delete();

        if($result){
            return $this->response(1, _e($this->typeName . ' successfully removed.'), null, null, ResponseStatus::NO_CONTENT);     
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
    



    

    

}
