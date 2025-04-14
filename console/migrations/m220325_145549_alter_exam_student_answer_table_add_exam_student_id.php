<?php

use yii\db\Migration;

/**
 * Class m220325_145549_alter_exam_student_answer_table_add_exam_student_id
 */
class m220325_145549_alter_exam_student_answer_table_add_exam_student_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->execute("ALTER TABLE `exam_student_answer` ADD `exam_student_id` int  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_145549_alter_exam_student_answer_table_add_exam_student_id cannot be reverted.\n";

        return false;
    }
}
