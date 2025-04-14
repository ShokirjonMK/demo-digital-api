<?php

use yii\db\Migration;

/**
 * Class m220809_120201_alter_exam_student_answer_sub_question_table_add_exam_student_and_student_id
 */
class m220809_120201_alter_exam_student_answer_sub_question_table_add_exam_student_and_student_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_student_answer_sub_question', 'exam_student_id', $this->integer(1)->after('id'));
        $this->addColumn('exam_student_answer_sub_question', 'student_id', $this->integer(1)->after('id'));
        $this->addColumn('exam_student_answer_sub_question', 'is_cheked', $this->integer(1)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_120201_alter_exam_student_answer_sub_question_table_add_exam_student_and_student_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_120201_alter_exam_student_answer_sub_question_table_add_exam_student_and_student_id cannot be reverted.\n";

        return false;
    }
    */
}
