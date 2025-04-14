<?php

namespace console\controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\console\Controller;
use yii\helpers\BaseConsole;

class ExcelController extends Controller
{

    public function actionTest()
    {
        $inputFileName = __DIR__ . '/abs.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file) {
            $spreadsheet = IOFactory::load($model->file->tempName);
            foreach ($data as $key => $row) {
                for ($i = 0; $i < count($row); $i++) {
                    echo $row[$i] . "\t";

                    // models save
                }
                echo "\n";
            }
        }
    }


}
