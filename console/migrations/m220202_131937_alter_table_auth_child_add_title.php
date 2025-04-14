<?php

use yii\db\Migration;

/**
 * Class m220202_131937_alter_table_auth_child_add_title
 */
class m220202_131937_alter_table_auth_child_add_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `auth_child` ADD `title` VARCHAR (255) NULL default NULL COMMENT 'rolni chiroyli nomi';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_131937_alter_table_auth_child_add_title cannot be reverted.\n";

        return false;
    }

}
