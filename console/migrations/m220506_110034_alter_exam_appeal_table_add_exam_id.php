<?php

use yii\db\Migration;

/**
 * Class m220506_110034_alter_exam_appeal_table_add_exam_id
 */
class m220506_110034_alter_exam_appeal_table_add_exam_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_appeal` ADD `exam_id` int default(0) COMMENT 'exam id';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220506_110034_alter_exam_appeal_table_add_exam_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220506_110034_alter_exam_appeal_table_add_exam_id cannot be reverted.\n";

        return false;
    }
    */
}
