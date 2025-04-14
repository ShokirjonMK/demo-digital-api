<?php

use yii\db\Migration;

/**
 * Class m220914_142008_time_table_add_time_option_id
 */
class m220914_142008_time_table_add_time_option_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('time_table', 'time_option_id', $this->integer()->null()->after('status'));

        // $this->addForeignKey('ttto_time_table_time_option_id', 'time_table', 'time_option_id', 'time_option', 'id');

   }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('time_table_time_option_id', 'time_table');

        echo "m220914_142008_time_table_add_time_option_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220914_142008_time_table_add_time_option_id cannot be reverted.\n";

        return false;
    }
    */
}
