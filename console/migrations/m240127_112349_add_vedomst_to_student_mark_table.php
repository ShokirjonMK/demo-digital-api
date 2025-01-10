<?php

use yii\db\Migration;

/**
 * Class m240127_112349_add_vedomst_to_student_mark_table
 */
class m240127_112349_add_vedomst_to_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_mark' , 'student_semestr_subject_vedomst_id', $this->integer()->null());
        $this->addForeignKey('mk_student_mark_table_student_semestr_subject_vedomst_table', 'student_mark', 'student_semestr_subject_vedomst_id', 'student_semestr_subject_vedomst', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240127_112349_add_vedomst_to_student_mark_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240127_112349_add_vedomst_to_student_mark_table cannot be reverted.\n";

        return false;
    }
    */
}
