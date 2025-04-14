<?php

namespace api\controllers;

use api\resources\System;
use base\ApiController;

class SystemController extends ApiController
{
    /**
     * Languages
     *
     * @return void
     */
    public function actionLanguage($alias = null)
    {
        $output = api_json_output();

        if ($alias == 'all') {
            $output = System::getAllLanguages();
        } elseif ($alias == 'one') {
            $output = System::getOneLanguage();
        } elseif ($alias == 'default') {
            $output = System::getDefaultLanguage();
        }

        return $this->output($output);
    }

    /**
     * Settings
     *
     * @return void
     */
    public function actionSettings($alias = null)
    {
        $output = api_json_output();

        if ($alias == 'all') {
            $output = System::getAllSettings();
        } elseif ($alias == 'one') {
            $output = System::getOneSetting();
        }

        return $this->output($output);
    }
}
