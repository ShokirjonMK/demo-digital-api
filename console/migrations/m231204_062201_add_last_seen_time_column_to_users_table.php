<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%users}}`.
 */
class m231204_062201_add_last_seen_time_column_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('users' , 'last_seen_time' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
    }
}
