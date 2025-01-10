<?php

use yii\db\Migration;

/**
 * Class m230921_100316_add_column_answer_option_id_table
 */
class m230921_100316_add_column_answer_option_id_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_access', 'work_load_id', $this->integer()->null());
        $this->addColumn('user_access', 'work_rate_id', $this->integer()->null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230921_100316_add_column_answer_option_id_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230921_100316_add_column_answer_option_id_table cannot be reverted.\n";

        return false;
    }
    */
}
