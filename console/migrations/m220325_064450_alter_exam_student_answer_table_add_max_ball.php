<?php

use yii\db\Migration;

/**
 * Class m220325_064450_alter_exam_student_answer_table_add_max_ball
 */
class m220325_064450_alter_exam_student_answer_table_add_max_ball extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer` ADD `max_ball` double  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_064450_alter_exam_student_answer_table_add_max_ball cannot be reverted.\n";

        return false;
    }

}
