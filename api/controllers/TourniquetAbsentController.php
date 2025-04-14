<?php

namespace api\controllers;

use base\ResponseStatus;
use common\models\model\TourniquetAbsent;
use common\models\model\Profile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;


class TourniquetAbsentController extends ApiActiveController
{
    public $modelClass = 'TourniquetAbsent';

    public $setFirstRecordAsKeys = true;

    public $leaveRecordByIndex = [];

    public function executeArrayLabel($sheetData)
    {
        $keys = ArrayHelper::remove($sheetData, '1');

        $new_data = [];

        foreach ($sheetData as $values) {
            $new_data[] = array_combine($keys, $values);
        }

        return $new_data;
    }

    public function actions()
    {
        return [];
    }

    public $table_name = 'tourniquet_absent';
    public $controller_name = 'TourniquetAbsent';


    public function actionIndex()
    {
        $model = new TourniquetAbsent();
        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);
        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreateGPT()
    {
        $errorAll = [];
        $post = [];

        $file = UploadedFile::getInstancesByName('file_excel');
        if (!$file) {
            return $this->response(0, _e('Excel file required'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        try {
            $inputFileType = IOFactory::identify($file[0]->tempName);
            $objReader = IOFactory::createReader($inputFileType);
            $objectPhpExcel = $objReader->load($file[0]->tempName);;

            $sheetDatas = $objectPhpExcel->getActiveSheet()->toArray(null, true, true, true);

            $sheetDatas = $this->executeArrayLabel($sheetDatas);

            if (!empty($this->getOnlyRecordByIndex)) {
                $sheetDatas = $this->executeGetOnlyRecords($sheetDatas, $this->getOnlyRecordByIndex);
            }
            if (!empty($this->leaveRecordByIndex)) {
                $sheetDatas = $this->executeLeaveRecords($sheetDatas, $this->leaveRecordByIndex);
            }

            $result = TourniquetAbsent::createItem($sheetDatas, $post);

            if (is_array($result)) {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }

            return $this->response(1, _e($this->controller_name . ' successfully updated.'), 'okey', null, ResponseStatus::OK);
        } catch (\Exception $e) {
            // $transaction->rollBack();
        }
    }

    public function actionCreate()
    {
        $data = [];
        $errorAll = [];

        $post = Yii::$app->request->post();

        $file = UploadedFile::getInstancesByName('file_excel');
        if (!$file) {
            return $this->response(0, _e('Excel file required'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        try {
            $inputFileType = IOFactory::identify($file[0]->tempName);
            $objReader = IOFactory::createReader($inputFileType);
            $objectPhpExcel = $objReader->load($file[0]->tempName);;

            $sheetDatas = [];

            $sheetDatas = $objectPhpExcel->getActiveSheet()->toArray(null, true, true, true);

            $sheetDatas = $this->executeArrayLabel($sheetDatas);


            if (!empty($this->getOnlyRecordByIndex)) {
                $sheetDatas = $this->executeGetOnlyRecords($sheetDatas, $this->getOnlyRecordByIndex);
            }
            if (!empty($this->leaveRecordByIndex)) {
                $sheetDatas = $this->executeLeaveRecords($sheetDatas, $this->leaveRecordByIndex);
            }

            $result = TourniquetAbsent::createItem($sheetDatas, $post);

            if (count($errorAll) > 0) {
                return $errorAll;
            }

            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' successfully updated.'), 'okey', null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } catch (\Exception $e) {
            // $transaction->rollBack();
        }
    }

    public function actionUpdate($id)
    {

        $model = TourniquetAbsent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);

        $result = TourniquetAbsent::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = TourniquetAbsent::find()->andWhere(['id' => $id, 'is_deleted' => 0])->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = TourniquetAbsent::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $model->delete();
        return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
    }
}
