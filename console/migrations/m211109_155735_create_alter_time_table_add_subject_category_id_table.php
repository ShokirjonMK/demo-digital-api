<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_time_table_add_subject_category_id}}`.
 */
class m211109_155735_create_alter_time_table_add_subject_category_id_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute("ALTER TABLE `time_table` add  `subject_category_id` INT(11) NOT NULL;");
        // $this->addForeignKey('wk_time_table_subject_category_id', 'time_table', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropTable('{{%alter_time_table_add_subject_category_id}}');
    }
}
