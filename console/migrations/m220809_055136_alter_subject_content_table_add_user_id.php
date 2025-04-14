<?php

use yii\db\Migration;

/**
 * Class m220809_055136_alter_subject_content_table_add_user_id
 */
class m220809_055136_alter_subject_content_table_add_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject_content` ADD `user_id` int (11) NULL default NULL COMMENT 'user id kimga qarashliligi' ;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_055136_alter_subject_content_table_add_user_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_055136_alter_subject_content_table_add_user_id cannot be reverted.\n";

        return false;
    }
    */
}
