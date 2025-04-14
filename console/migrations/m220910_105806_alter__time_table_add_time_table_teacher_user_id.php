<?php

use yii\db\Migration;

/**
 * Class m220910_105806_alter__time_table_add_time_table_teacher_user_id
 */
class m220910_105806_alter__time_table_add_time_table_teacher_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('time_table', 'teacher_user_id', $this->integer()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220910_105806_alter__time_table_add_time_table_teacher_user_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220910_105806_alter__time_table_add_time_table_teacher_user_id cannot be reverted.\n";

        return false;
    }
    */
}
