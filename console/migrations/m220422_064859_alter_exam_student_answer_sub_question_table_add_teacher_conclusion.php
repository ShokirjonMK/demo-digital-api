<?php

use yii\db\Migration;

/**
 * Class m220422_064859_alter_exam_student_answer_sub_question_table_add_teacher_conclusion
 */
class m220422_064859_alter_exam_student_answer_sub_question_table_add_teacher_conclusion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer_sub_question` ADD `teacher_conclusion` text NULL COMMENT ' xulosa';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220422_064859_alter_exam_student_answer_sub_question_table_add_teacher_conclusion cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220422_064859_alter_exam_student_answer_sub_question_table_add_teacher_conclusion cannot be reverted.\n";

        return false;
    }
    */
}
