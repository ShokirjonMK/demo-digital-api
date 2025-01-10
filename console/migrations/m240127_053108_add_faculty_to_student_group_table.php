<?php

use yii\db\Migration;

/**
 * Class m240127_053108_add_faculty_to_student_group_table
 */
class m240127_053108_add_faculty_to_student_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_group', 'faculty_id' , $this->integer()->null());
        $this->addColumn('student_group', 'direction_id' , $this->integer()->null());
        $this->addForeignKey('mk_student_group_table_faculty_table', 'student_group', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_student_group_table_direction_table', 'student_group', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240127_053108_add_faculty_to_student_group_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240127_053108_add_faculty_to_student_group_table cannot be reverted.\n";

        return false;
    }
    */
}
