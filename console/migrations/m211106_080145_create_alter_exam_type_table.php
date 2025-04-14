<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_exam_type}}`.
 */
class m211106_080145_create_alter_exam_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_semestr_exams_type` CHANGE `max-ball` `max_ball` INT(11) NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_exam_type}}');
    }
}
