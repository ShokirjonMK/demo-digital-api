<?php

use yii\db\Migration;

/**
 * Class m211225_073238_alter_exam_table_add_protected
 */
class m211225_073238_alter_exam_table_add_protected extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `is_protected` int null default 0;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211225_073238_alter_exam_table_add_protected cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211225_073238_alter_exam_table_add_protected cannot be reverted.\n";

        return false;
    }
    */
}
