<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_time_table_add_parent_id}}`.
 */
class m211109_153640_create_alter_time_table_add_parent_id_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `time_table` ADD `parent_id` INT NULL AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_time_table_add_parent_id}}');
    }
}
