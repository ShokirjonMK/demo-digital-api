<?php

use yii\db\Migration;

/**
 * Class m241202_063741_add_json_to_student_mark_table
 */
class m241202_063741_add_json_to_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_mark' , 'student_semestr_subject_id' , $this->integer()->null());
        $this->addColumn('student_mark' , 'json_ball' , $this->string(255)->null());
        $this->addColumn('student_mark' , 'json_attend' , $this->string(255)->null());

        $this->addForeignKey('mk_student_mark_table_student_semestr_subject_table', 'student_mark', 'student_semestr_subject_id', 'student_semestr_subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241202_063741_add_json_to_student_mark_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241202_063741_add_json_to_student_mark_table cannot be reverted.\n";

        return false;
    }
    */
}
