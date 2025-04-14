<?php

use yii\db\Migration;

/**
 * Class m220203_091329_alter_table_profile_add_citizenship_id
 */
class m220203_091329_alter_table_profile_add_citizenship_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `profile` ADD `citizenship_id` int default 1 COMMENT 'citizenship_id fuqarolik turi';");

        $this->addForeignKey('pc_profile_citizenship_mk', 'profile', 'citizenship_id', 'citizenship', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('pc_profile_citizenship_mk', 'profile');
        echo "m220203_091329_alter_table_profile_add_citizenship_id cannot be reverted.\n";

        return false;
    }
}
