<?php

use yii\db\Migration;

/**
 * Class m220801_054452_alter_exam_appeal_table_add_is_changed
 */
class m220801_054452_alter_exam_appeal_table_add_is_changed extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_appeal` ADD `is_changed` int default 0 COMMENT 'o-zgartirilganmi ';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220801_054452_alter_exam_appeal_table_add_is_changed cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220801_054452_alter_exam_appeal_table_add_is_changed cannot be reverted.\n";

        return false;
    }
    */
}
