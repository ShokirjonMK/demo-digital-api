<?php

use yii\db\Migration;

/**
 * Class m220703_083534_alter_exam_student_answer_sub_question_add_old_ball_appeal_teacher_conclution
 */
class m220703_083534_alter_exam_student_answer_sub_question_add_old_ball_appeal_teacher_conclution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer_sub_question` ADD `old_ball` double null COMMENT 'eski ball';");
        $this->execute("ALTER TABLE `exam_student_answer_sub_question` ADD `appeal_teacher_conclusion` text null COMMENT 'appeal_teacher_conclusion';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220703_083534_alter_exam_student_answer_sub_question_add_old_ball_appeal_teacher_conclution cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220703_083534_alter_exam_student_answer_sub_question_add_old_ball_appeal_teacher_conclution cannot be reverted.\n";

        return false;
    }
    */
}
