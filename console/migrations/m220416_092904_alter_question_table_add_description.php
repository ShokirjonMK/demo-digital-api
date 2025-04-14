<?php

use yii\db\Migration;

/**
 * Class m220416_092904_alter_question_table_add_description
 */
class m220416_092904_alter_question_table_add_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `question` ADD `description` text NULL COMMENT 'izohlar uchun';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220416_092904_alter_question_table_add_description cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220416_092904_alter_question_table_add_description cannot be reverted.\n";

        return false;
    }
    */
}
