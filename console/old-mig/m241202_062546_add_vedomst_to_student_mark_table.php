<?php

use yii\db\Migration;

/**
 * Class m241202_062546_add_vedomst_to_student_mark_table
 */
class m241202_062546_add_vedomst_to_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_mark' , 'vedomst' , $this->integer()->defaultValue(1));
        $this->addColumn('student_mark' , 'user_id' , $this->integer()->null());
        $this->addColumn('student_mark' , 'subject_semestr_id' , $this->integer()->null());

        $this->addColumn('student_mark' , 'start_time' , $this->integer()->null());
        $this->addColumn('student_mark' , 'finish_time' , $this->integer()->null());

        $this->dropColumn('student_mark', 'passed');
        $this->dropColumn('student_mark', 'attend');

        $this->dropForeignKey(
            'mk_student_mark_table_student_semestr_subject_vedomst_table',
            'student_mark'
        );

        $this->dropColumn('student_mark', 'student_semestr_subject_vedomst_id');
        $this->addForeignKey('mk_student_mark_table_user_table', 'student_mark', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_student_mark_table_subject_semestr_table', 'student_mark', 'subject_semestr_id', 'subject_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241202_062546_add_vedomst_to_student_mark_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241202_062546_add_vedomst_to_student_mark_table cannot be reverted.\n";

        return false;
    }
    */
}
