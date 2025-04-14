<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_time}}`.
 */
class m211106_091620_create_alter_time_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `time_table` CHANGE `semestr_id` `semester_id` INT(11) NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_time}}');
    }
}
