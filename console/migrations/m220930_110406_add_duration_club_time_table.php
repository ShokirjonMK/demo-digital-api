<?php

use yii\db\Migration;

/**
 * Class m220930_110406_add_duration_club_time_table
 */
class m220930_110406_add_duration_club_time_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('club_time', 'duration', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_110406_add_duration_club_time_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220930_110406_add_duration_club_time_table cannot be reverted.\n";

        return false;
    }
    */
}
