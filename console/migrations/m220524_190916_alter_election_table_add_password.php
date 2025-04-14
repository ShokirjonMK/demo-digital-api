<?php

use yii\db\Migration;

/**
 * Class m220524_190916_alter_election_table_add_password
 */
class m220524_190916_alter_election_table_add_password extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `election` ADD `password` varchar(255) null COMMENT 'password' after `id`;");


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220524_190916_alter_election_table_add_password cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220524_190916_alter_election_table_add_password cannot be reverted.\n";

        return false;
    }
    */
}
