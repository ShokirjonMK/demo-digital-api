<?php

use yii\db\Migration;

/**
 * Class m220915_150503_student_time_table_add_time_option_and_student_time_option
 */
class m220915_150503_student_time_table_add_time_option_and_student_time_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('student_time_table', 'student_time_option_id', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220915_150503_student_time_table_add_time_option_and_student_time_option cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220915_150503_student_time_table_add_time_option_and_student_time_option cannot be reverted.\n";

        return false;
    }
    */
}
