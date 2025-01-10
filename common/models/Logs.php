<?php

namespace common\models;

use Yii;

/**
 * This is the model class for logs.
 */
class Logs
{
    /**
     * Get admin logs
     *
     * @param array $args
     * @return object
     */
    public static function getAdminLogs($args = array())
    {
        $query = LogsAdmin::find();

        if (is_array($args) && $args) {
            $order_by = array('id' => 'DESC');

            if (isset($args['where']) && $args['where']) {
                $query->where($args['where']);
            }

            if (isset($args['andWhere']) && $args['andWhere']) {
                $query->andWhere($args['andWhere']);
            }

            if (isset($args['with']) && $args['with']) {
                $query->with($args['with']);
            }

            if (isset($args['order_by']) && $args['order_by']) {
                $order_by = $args['order_by'];
            }

            $query->orderBy($order_by);
        }

        return $query->all();
    }

    /**
     * Set admin log
     *
     * @param array $args
     * @return void
     */
    public static function setAdminLog($args = array())
    {
        return self::setLog('admin', $args);
    }

    /**
     * Set seller log
     *
     * @param array $args
     * @return void
     */
    public static function setSellerLog($args = array())
    {
        return self::setLog('seller', $args);
    }

    /**
     * Set log
     *
     * @param [type] $table
     * @param [type] $args
     * @return void
     */
    public static function setLog($table, $args)
    {
        if ($table && is_array($args) && $args) {
            $model = false;

            if ($table == 'admin') {
                $model = new LogsAdmin();
            }

            if ($model) {
                $model->res_id = 0;
                $model->data = '';
                $model->type = '';
                $model->action = '';
                $model->created_on = date('Y-m-d H:i:s');
                $model->user_id = Yii::$app->user->getId();

                // Session data
                $request = Yii::$app->request;
                $ip_address = $request->userIP;
                $referrer = $request->referrer;

                $session_data = getBrowser();
                $session_data['ip_address'] = $ip_address;
                $session_data['referrer'] = $referrer;
                $session_data['date'] = date('Y-m-d H:i:s');

                $model->ip_address = $ip_address;
                $model->browser = json_encode($session_data);

                if (isset($args['user_id']) && is_numeric($args['user_id']) && $args['user_id'] > 0) {
                    $model->user_id = $args['user_id'];
                }

                if (isset($args['res_id']) && is_numeric($args['res_id'])) {
                    $model->res_id = $args['res_id'];
                }

                if (isset($args['action'])) {
                    $model->action = $args['action'];
                }

                if (isset($args['type'])) {
                    $model->type = $args['type'];
                }

                if (isset($args['data'])) {
                    $model->data = $args['data'];
                }

                if (isset($args['ip_address']) && $args['ip_address']) {
                    $model->ip_address = $args['ip_address'];
                }

                if (isset($args['browser']) && $args['browser']) {
                    $model->browser = $args['browser'];
                }

                if (isset($args['created_on']) && $args['created_on']) {
                    $model->created_on = $args['created_on'];
                }

                $model->save(false);
            }
        }
    }

    /**
     * Log item view
     *
     * @param [type] $item
     * @return object
     */
    public static function logItemView($item)
    {
        $output = new \stdClass();

        if ($item) {
            foreach ($item as $key => $value) {
                $output->$key = $value;
            }

            $action = $item->action;
            $type = $item->type;

            $types_name = array(
                'brand' => 'Brand',
                'category' => 'Category',
                'page' => 'Page',
                'post' => 'Post',
                'post_category' => 'Post Category',
                'post_tag' => 'Post Tag',
                'shop' => 'Shop',
                'user' => 'User',
            );

            $actions_name = array(
                'activate' => 'Activated',
                'block' => 'Blocked',
                'create' => 'Created',
                'delete' => 'Deleted',
                'restore' => 'Restored',
                'trash' => 'Sent to trash',
                'update' => 'Updated',
            );

            if (isset($types_name[$type])) {
                $output->type_name = $types_name[$type];
            } else {
                $output->type_name = mb_convert_case($type, MB_CASE_TITLE, "UTF-8");
            }

            if (isset($actions_name[$action])) {
                $output->action_name = $actions_name[$action];
            } else {
                $output->action_name = mb_convert_case($action, MB_CASE_TITLE, "UTF-8");
            }
        }

        return $output;
    }
}
