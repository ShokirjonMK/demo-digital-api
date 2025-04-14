<?php

use yii\db\Migration;

/**
 * Class m220203_094711_alter_table_direction_add_code
 */
class m220203_094711_alter_table_direction_add_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `direction` ADD `code` VARCHAR (255) NULL default '5240100' COMMENT 'yonalish kodi';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_094711_alter_table_direction_add_code cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_094711_alter_table_direction_add_code cannot be reverted.\n";

        return false;
    }
    */
}
