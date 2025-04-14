<?php

use yii\db\Migration;

/**
 * Class m220909_133037_alter_student_time_table_add_time_table_staff
 */
class m220909_133037_alter_student_time_table_add_time_table_staff extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('student_time_table', 'teacher_access_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'language_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'course_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'semester_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'edu_year_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'subject_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'room_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'para_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'week_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'edu_semester_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'subject_category_id', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220909_133037_alter_student_time_table_add_time_table_staff cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220909_133037_alter_student_time_table_add_time_table_staff cannot be reverted.\n";

        return false;
    }
    */
}
