<?php

namespace console\controllers;

use yii\console\Controller;
use common\models\model\ActionLog;
use common\models\model\Turniket;
use common\models\model\TurniketData;
use Yii;
use yii\helpers\Console;

class TurniketController extends Controller
{
    const MONTH_LOG = 'month-logs/';
    const DAIRLY_LOG = 'dairly/';

    public function actionStart()
    {
        if (!defined('LOGS_PATH')) {
            $defile_LOGS = "\n\ndefine('LOGS_PATH', '/logs' . DS);";
            file_put_contents(__DIR__ . '/../../config.boot.php', $defile_LOGS, FILE_APPEND | LOCK_EX);
        }

        //        if (!file_exists(LOGS_PATH . self::MONTH_LOG)) {
        //            mkdir(LOGS_PATH . self::MONTH_LOG, 0777, true);
        //        }
        //
        //        if (!file_exists(LOGS_PATH . self::DAIRLY_LOG)) {
        //            mkdir(LOGS_PATH . self::DAIRLY_LOG, 0777, true);
        //        }

        $this->setLogStatus(true);
        $this->stdout("\n\n Logging started ...\n\n", Console::FG_GREEN);
    }

    private function setLogStatus($value)
    {
        $params = require __DIR__ . '/../../config.inc.php';
        $params['mkStatusLogging'] = $value;
        file_put_contents(__DIR__ . '/../../config.inc.php', "<?php\n\nreturn " . var_export($params, true) . ";\n");
        Yii::$app->params['mkStatusLogging'] = $value;
    }

    public function actionStop()
    {
        $this->setLogStatus(false);
        $this->stdout("\n\n Logging stopped ...\n\n", Console::FG_RED);
    }

    public function actionStatus()
    {
        $this->setLogStatus(false);
        $this->stdout("\n\n " . Yii::$app->params['mkStatusLogging'] . "  \n\n", Console::BG_YELLOW);
    }

    public function actionCreate()
    {
        $dairly_logs_dir = LOGS_PATH . self::DAIRLY_LOG;
        if (!file_exists($dairly_logs_dir)) {
            mkdir($dairly_logs_dir, 0777, true);
        }
        $month_logs = LOGS_PATH . self::MONTH_LOG;
        if (!file_exists($month_logs)) {
            mkdir($month_logs, 0777, true);
        }

        $today_log_file = "log-" . date("Y-m-d") . ".json";

        $folder_url = \Yii::getAlias($dairly_logs_dir);
        $scan = scandir($folder_url);
        $logs = [];

        foreach ($scan as $file) {

            if (!is_dir(\Yii::getAlias($dairly_logs_dir . '/' . $file))) {
                if ($today_log_file != $file) {
                    $logs_date = substr($file, 4, 10);
                    $base_directory = \Yii::getAlias($dairly_logs_dir . $file);
                    if (file_exists($base_directory)) {
                        $folder = date('Y-m', strtotime($logs_date));
                        $month_folder = \Yii::getAlias($month_logs . $folder);
                        if (!is_dir($month_folder)) {
                            mkdir(\Yii::getAlias($month_logs . $folder), 0777, true);
                        }
                        rename(\Yii::getAlias($dairly_logs_dir) . $file, \Yii::getAlias($month_logs . $folder . '/') . $file);
                    }
                }
            }
        }

        //        dd($file);

        $base_directory = \Yii::getAlias($dairly_logs_dir . 'log-' . date("Y-m-d", strtotime("-1 days")) . '.json');
        if (file_exists($base_directory)) {
            $data = file_get_contents($base_directory, true);
            $str = substr_replace($data, "", 0, 1);

            $str = "[" . str_replace("\n", '', $str) . "]";

            $bigData = json_decode($str);

            foreach ($bigData as $datum) {
                $action_log = new ActionLog();

                $action_log->save();
            }
        }
    }

    public function actionCreateDate($date)
    {
        $date = date("Y-m-d", strtotime($date));
        $date_year_month = date("Y-m", strtotime($date));
        $date_day = date("d", strtotime($date));
        $month_logs = LOGS_PATH . self::MONTH_LOG;
        $folder_url = \Yii::getAlias($month_logs . $date_year_month);

        if (!file_exists($month_logs . $date_year_month)) {
            mkdir($month_logs . $date_year_month, 0777, true);
        }

        $log_file_date = LOGS_PATH . self::DAIRLY_LOG . 'log-' . $date . '.json';
        if (!file_exists($log_file_date)) {
            $this->stdout("\n\nLog file not found (" . $date . ")", Console::FG_RED);
            $this->stdout("\n\n");
            die();
        }


        $data = file_get_contents($log_file_date, true);
        $str = substr_replace($data, "", 0, 1);

        $str = "[" . str_replace("\n", '', $str) . "]";

        $bigData = json_decode($str);

        foreach ($bigData as $datum) {
            // dd($datum);
            $action_log = new ActionLog();
            $action_log->user_id = $datum->user_id;
            $action_log->data = $datum->data;

            $action_log->status = $datum->status;
            $action_log->message = $datum->message;
            $action_log->browser = $datum->browser;
            $action_log->host = $datum->host;
            $action_log->controller = $datum->controller;
            $action_log->action = $datum->action;
            $action_log->method = $datum->method;
            $action_log->get_data = $datum->get_data;
            $action_log->post_data = $datum->post_data;
            $action_log->created_at = $datum->created_at;
            $action_log->created_on = date("d-m-Y", strtotime($date));
            $action_log->save();

            // dd($action_log);
        }
    }

    public function actionCreateBase2($date)
    {
        $date_year_month = date("Y-m", strtotime($date));
        $date_day = date("d", strtotime($date));
        $month_logs = '@api/web/logs/';

        $folder_url = \Yii::getAlias($month_logs . $date_year_month);
        $scan = scandir($folder_url);
        $logs = [];
        foreach ($scan as $file) {
            if (!is_dir(\Yii::getAlias($month_logs . $date_year_month . '/' . $file))) {
                $date_log = substr($file, 12, 2);
                if ($date_day == $date_log) {
                    $logs['logs'] = $file;
                }
            }
        }
        foreach ($logs as $log) {
            $base_directory = \Yii::getAlias($month_logs . $date_year_month . '/' . $log);

            if (file_exists($base_directory)) {
                $data = file_get_contents($base_directory, true);
                $str = substr_replace($data, "", 0, 1);

                $str = "[" . str_replace("\n", '', $str) . "]";

                $bigData = json_decode($str);

                foreach ($bigData as $datum) {
                    $action_log = new ActionLog();

                    $action_log->log_date = date("d-m-Y", strtotime($date));
                    $action_log->save();
                }
                // base save
            }
        }
    }


    public function actionDate($date)
    {
        $main_path = MAIN_STORAGE_PATH . 'turniket_log';

        $year = $main_path . "/year-" . date("Y", strtotime($date));
        $month = $year . '/month-' . date("m", strtotime($date));
        $day = $month . '/day-' . date("d", strtotime($date));

        $turniketFilePath = $day . "/turniket-" . date("Y-m-d", strtotime($date)) . ".json";
        // local
        // $turniketFilePath = MAIN_STORAGE_PATH .  "/turniket-" . date("Y-m-d", strtotime($date)) . ".json";
        // $turniketFilePath = MAIN_STORAGE_PATH . 'turniket' . "/turniket-" . date("Y-m-d", strtotime($date)) . ".json";


        $fileUrl = \Yii::getAlias($turniketFilePath);
        if (file_exists($fileUrl)) {
            $this->stdout("\n\n File found $turniketFilePath ...\n", Console::BG_YELLOW);
            $data = file_get_contents($fileUrl, true);
            $str = substr_replace($data, "", 0, 1);
            $str = "[" . str_replace("\n", '', $str) . "]";
            $bigData = json_decode($str);
            if ($bigData !== null) {
                $this->stdout("\n\n Begins  ...\n", Console::BG_YELLOW);
                foreach ($bigData as $dataOne) {
                    $turniketData = new TurniketData();
                    $turniketData->data = $dataOne;
                    $turniketData->reader = $dataOne->params->events[0]->data->readerIndexCode;
                    $turniketData->turniket_id = $dataOne->params->events[0]->data->personId;
                    $turniketData->passport_pin = $dataOne->params->events[0]->data->personCode;
                    $turniketData->date = date("Y-m-d", strtotime($dataOne->params->events[0]->happenTime));
                    $turniketData->time = strtotime($dataOne->params->events[0]->happenTime);
                    $turniketData->in_out = (stripos($dataOne->params->events[0]->data->readerName, 'kirish') !== false) ? 1 : ((stripos($dataOne->params->events[0]->data->readerName, 'chiqish') !== false) ? 2 : 0);
                    // $turniketData->user_id = $turniketData->profile->user_id;
                    $turniketData->type = (strpos(strtolower($dataOne['params']['events'][0]['srcName']), 'ttj') !== false) ? 2 : 1;

                    $turniketData->save(false);
                    $turniket = Turniket::findOne([
                        'turniket_id' => $turniketData->turniket_id,
                        'date' => $turniketData->date
                    ]);
                    if ($turniket) {
                        $turniket->go_out_time = $turniketData->time;
                        // $turniket->user_id = $turniketData->profile->user_id;
                    } else {
                        $turniket = new Turniket();
                        $turniket->turniket_id = $turniketData->turniket_id;
                        $turniket->passport_pin = $turniketData->profile->passport_pin;
                        // $turniket->user_id = $turniketData->profile->user_id;
                        $turniket->date = $turniketData->date;
                        $turniket->go_in_time = $turniketData->time;
                    }

                    $turniket->save(false);
                }
                $this->stdout("\n\n Success ...\n\n", Console::FG_GREEN);
            } else {
                $this->stdout("\n\n Data is null :)  ...\n\n", Console::BG_GREY);
            }
        }
    }


    public function actionDeleteBase($date)
    {
        $logs = ActionLog::deleteAll(['log_date' => $date]);
    }
}
