<?php

namespace common\models;

use base\Container;
use Yii;
use yii\web\Cookie;

/**
 * Analytics model.
 */
class Analytics
{
    /**
     * Ajax actions
     *
     * @return mixed
     */
    public static function ajax()
    {
        $action = Yii::$app->request->post('action');

        self::saveUser();
        self::saveSession();

        if ($action == 'set') {
            $key = Yii::$app->request->post('key');
            $value = Yii::$app->request->post('value');
            $referrer = Yii::$app->request->post('referrer');
            $status_code = Yii::$app->request->post('status_code');

            self::saveView($key, $value, $referrer, $status_code);
        }
    }

    /**
     * Save view
     *
     * @return mixed
     */
    public static function saveView($key, $value, $referrer = '', $status_code = 0)
    {
        $hash = self::getUserHash();

        if ($hash) {
            $model = new AnalyticsViews();
            $model->uid = $hash;
            $model->value = $value;
            $model->type = $key;
            $model->referrer = $referrer;
            $model->status_code = $status_code;
            $model->created_on = date('Y-m-d H:i:s');
            $model->save(false);
        }
    }

    /**
     * Save user
     *
     * @return mixed
     */
    public static function saveUser()
    {
        $hash = self::getUserHash();

        if ($hash) {
            $user = AnalyticsUsers::find()
                ->where(['uid' => $hash])
                ->one();

            if ($user) {
                $user->updated_on = date('Y-m-d H:i:s');
                $user->save(false);
            } else {
                $browser = getBrowser();
                $ip_address = Yii::$app->request->userIP;
                $ip_response = getIpAddressData($ip_address);
                $ip_data = array_value($ip_response, 'data');
                $country_code = array_value($ip_data, 'country_code', 'Unknown');

                $model = new AnalyticsUsers();
                $model->uid = $hash;
                $model->ip_address = $ip_address;
                $model->country_code = $country_code;
                $model->uagent = array_value($browser, 'user_agent');
                $model->ua_device = array_value($browser, 'device');
                $model->ua_os = array_value($browser, 'platform');
                $model->ua_browser = array_value($browser, 'browser_name');
                $model->created_on = date('Y-m-d H:i:s');
                $model->updated_on = date('Y-m-d H:i:s');
                $model->save(false);
            }
        }
    }

    /**
     * Save session
     *
     * @return mixed
     */
    public static function saveSession()
    {
        $hash = self::getUserHash();
        $session_key = self::getUserSession();

        if ($hash && $session_key) {
            $session = AnalyticsSessions::find()
                ->where(['uid' => $hash, 'session_key' => $session_key])
                ->one();

            if ($session) {
                $session->updated_on = date('Y-m-d H:i:s');
                $session->save(false);
            } else {
                $model = new AnalyticsSessions();
                $model->uid = $hash;
                $model->session_key = $session_key;
                $model->created_on = date('Y-m-d H:i:s');
                $model->updated_on = date('Y-m-d H:i:s');
                $model->save(false);
            }
        }
    }

    /**
     * Get user hash
     *
     * @return mixed
     */
    public static function getUserHash()
    {
        $cookies = Yii::$app->request->cookies;
        return $cookies->getValue('auid');
    }

    /**
     * Set hash to user
     *
     * @return void
     */
    public static function setUserHash()
    {
        $cookie_hash = false;
        $cookies = Yii::$app->request->cookies;
        $cookie_item = $cookies->getValue('auid');

        if ($cookie_item != null) {
            $cookie_hash = $cookie_item;
        }

        if (!$cookie_hash || empty($cookie_hash)) {
            $str = 'auid-salt-';
            $str .= _random_string('alnum', 15);
            $str .= '-' . date('Y-m-d-H:i:s', strtotime('+1 year'));
            $str .= '-' . _random_string('alpha', 10);
            $str .= '-' . strtotime('now');
            $str .= '-' . rand(10000, 99999);
            $hash = md5($str) . rand(100, 999);

            $cookie = new Cookie([
                'name' => 'auid',
                'value' => $hash,
                'expire' => time() + (60 * 60 * 24 * 365),
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Get user session
     *
     * @return mixed
     */
    public static function getUserSession()
    {
        $cookies = Yii::$app->request->cookies;
        return $cookies->getValue('auid-session');
    }

    /**
     * Set session to user
     *
     * @return void
     */
    public static function setUserSession()
    {
        $cookie_hash = false;
        $cookies = Yii::$app->request->cookies;
        $cookie_item = $cookies->getValue('auid-session');

        if ($cookie_item != null) {
            $cookie_hash = $cookie_item;

            $cookie = new Cookie([
                'name' => 'auid-session',
                'value' => $cookie_hash,
                'expire' => time() + (60 * 30),
            ]);

            Yii::$app->response->cookies->add($cookie);
        }

        if (!$cookie_hash || empty($cookie_hash)) {
            $str = 'auid-session-';
            $str .= _random_string('alnum', 15);
            $str .= '-' . date('Y-m-d-H:i:s', strtotime('+30 min'));
            $str .= '-' . _random_string('alpha', 10);
            $str .= '-' . strtotime('now');
            $str .= '-' . rand(10000, 99999);
            $hash = md5($str) . rand(100, 999);

            $cookie = new Cookie([
                'name' => 'auid-session',
                'value' => $hash,
                'expire' => time() + (60 * 30),
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Set js
     *
     * @return void
     */
    public static function setJS($app)
    {
        $app->registerJsFile(
            assets_url('theme/js/analytics.min.js'),
            [
                'depends' => [\yii\web\JqueryAsset::className()],
                'position' => \yii\web\View::POS_HEAD,
            ]
        );

        $analytics_js = Container::get('analytics-js');
        $status_code = Yii::$app->response->statusCode;
        $script = $analytics_js ? $analytics_js : "web_analytics.init('page');";

        return "<script type=\"text/javascript\">
            web_analytics.referrer = document.referrer;
            web_analytics.current_link = window.location.href;
            web_analytics.status_code = $status_code;
            $script
        </script>";
    }
}
