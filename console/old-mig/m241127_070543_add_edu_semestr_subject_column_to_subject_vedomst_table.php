<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subject_vedomst}}`.
 */
class m241127_070543_add_edu_semestr_subject_column_to_subject_vedomst_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_semestr_subject_vedomst' , 'edu_semestr_subject_id' , $this->integer()->null());
        $this->addForeignKey('mk_edu_subject_table_student_semestr_vedomst_table', 'student_semestr_subject_vedomst', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
