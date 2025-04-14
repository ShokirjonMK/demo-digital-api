<?php

use yii\db\Migration;

/**
 * Class m220422_051817_alter_exam_student_answer_table_add_teacher_conclusion
 */
class m220422_051817_alter_exam_student_answer_table_add_teacher_conclusion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer` ADD `teacher_conclusion` text NULL COMMENT 'umumiy xulosa';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220422_051817_alter_exam_student_answer_table_add_teacher_conclusion cannot be reverted.\n";

        return false;
    }
}
