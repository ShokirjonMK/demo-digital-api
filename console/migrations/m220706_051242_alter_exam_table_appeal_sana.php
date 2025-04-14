<?php

use yii\db\Migration;

/**
 * Class m220706_051242_alter_exam_table_appeal_sana
 */
class m220706_051242_alter_exam_table_appeal_sana extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `appeal_start` int null COMMENT 'elon qilingan sanasi';");
        $this->execute("ALTER TABLE `exam` ADD `appeal_finish` int null COMMENT 'elon qilingan sanasi';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220706_051242_alter_exam_table_appeal_sana cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220706_051242_alter_exam_table_appeal_sana cannot be reverted.\n";

        return false;
    }
    */
}
