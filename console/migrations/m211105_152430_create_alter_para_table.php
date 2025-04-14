<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_para}}`.
 */
class m211105_152430_create_alter_para_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `para` ADD `start_time` VARCHAR(5) NOT NULL AFTER `id`, ADD `end_time` VARCHAR(5) NOT NULL AFTER `start_time`");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
