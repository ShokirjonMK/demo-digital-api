<?php

use yii\db\Migration;

/**
 * Class m220911_120416_alter_time_table_add_time_table_edu_pan
 */
class m220911_120416_alter_time_table_add_time_table_edu_pan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('student_time_table', 'edu_plan_id', $this->integer()->null()->after('status'));
        // $this->addColumn('student_time_table', 'building_id', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220911_120416_alter_time_table_add_time_table_edu_pan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220911_120416_alter_time_table_add_time_table_edu_pan cannot be reverted.\n";

        return false;
    }
    */
}
