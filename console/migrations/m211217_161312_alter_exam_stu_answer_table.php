<?php

use yii\db\Migration;

/**
 * Class m211217_161312_alter_exam_stu_answer_table
 */
class m211217_161312_alter_exam_stu_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      //  $this->execute("ALTER TABLE `exam_student_answer` CHANGE `exam_question_id` `question_id` INT(11) NOT NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211217_161312_alter_exam_stu_answer_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211217_161312_alter_exam_stu_answer_table cannot be reverted.\n";

        return false;
    }
    */
}
