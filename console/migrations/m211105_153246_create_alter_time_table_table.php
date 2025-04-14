<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_time_table}}`.
 */
class m211105_153246_create_alter_time_table_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->execute("ALTER TABLE `time_table` ADD `week_id` INT NOT NULL AFTER `language_id`");

        $this->addForeignKey('wk_time_table_week_id','time_table','week_id','week','id');

    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropForeignKey('wk_time_table_week_id','time_table');
    }
}
