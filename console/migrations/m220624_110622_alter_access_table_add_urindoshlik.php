<?php

use yii\db\Migration;

/**
 * Class m220624_110622_alter_access_table_add_urindoshlik
 */
class m220624_110622_alter_access_table_add_urindoshlik extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `user_access` ADD `work_type` int null COMMENT 'work_type urindoshlik';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_110622_alter_access_table_add_urindoshlik cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_110622_alter_access_table_add_urindoshlik cannot be reverted.\n";

        return false;
    }
    */
}
