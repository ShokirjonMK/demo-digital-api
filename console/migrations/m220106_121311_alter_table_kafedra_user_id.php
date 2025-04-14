<?php

use yii\db\Migration;

/**
 * Class m220106_121311_alter_table_kafedra_user_id
 */
class m220106_121311_alter_table_kafedra_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `kafedra` ADD `user_id` INT(11)  NULL COMMENT 'Lead of kafedra or Mudir' ;");

        $this->addForeignKey('fu_kafedra_user_id', 'kafedra', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fu_kafedra_user_id', 'kafedra');

        echo "m220106_121311_alter_table_kafedra_user_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220106_121311_alter_table_kafedra_user_id cannot be reverted.\n";

        return false;
    }
    */
}
