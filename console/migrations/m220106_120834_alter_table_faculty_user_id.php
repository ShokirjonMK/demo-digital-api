<?php

use yii\db\Migration;

/**
 * Class m220106_120834_alter_table_faculty_user_id
 */
class m220106_120834_alter_table_faculty_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `faculty` ADD `user_id` INT(11)  NULL COMMENT 'Lead of faculty or Dean (dekan)' ;");

        $this->addForeignKey('fu_faculty_user_id', 'faculty', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fu_faculty_user_id', 'faculty');

        echo "m220106_120834_alter_table_faculty_user_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220106_120834_alter_table_faculty_user_id cannot be reverted.\n";

        return false;
    }
    */
}
