<?php

use yii\db\Migration;

/**
 * Class m240127_050304_add_plan_to_student_mark_table
 */
class m240127_050304_add_plan_to_student_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('student_group', 'edu_year_id' , $this->integer()->null());
        $this->addColumn('student_group', 'edu_plan_id' , $this->integer()->null());
        $this->addColumn('student_group', 'edu_semestr_id' , $this->integer()->null());
        $this->addColumn('student_group', 'edu_form_id' , $this->integer()->null());
        $this->addColumn('student_group', 'semestr_id' , $this->integer()->null());
        $this->addColumn('student_group', 'course_id' , $this->integer()->null());
        $this->addForeignKey('mk_student_group_table_edu_year_table', 'student_group', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_group_table_edu_plan_table', 'student_group', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_student_group_table_edu_semestr_table', 'student_group', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_student_group_table_semestr_table', 'student_group', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_student_group_table_course_table', 'student_group', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_student_group_table_edu_form_table', 'student_group', 'edu_form_id', 'edu_form', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240127_050304_add_plan_to_student_mark_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240127_050304_add_plan_to_student_mark_table cannot be reverted.\n";

        return false;
    }
    */
}
