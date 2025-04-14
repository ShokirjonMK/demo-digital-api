<?php

use yii\db\Migration;

/**
 * Class m220910_070330_alter_student_time_table_add_time_table_staff
 */
class m220910_070330_alter_student_time_table_add_time_table_staff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('student_time_table', 'time_table_parent_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'time_table_lecture_id', $this->integer()->null()->after('status'));
        // // $this->addColumn('student_time_table', 'teacher_access_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'teacher_user_id', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220910_070330_alter_student_time_table_add_time_table_staff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220910_070330_alter_student_time_table_add_time_table_staff cannot be reverted.\n";

        return false;
    }
    */
}
