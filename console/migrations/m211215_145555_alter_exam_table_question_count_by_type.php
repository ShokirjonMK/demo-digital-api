<?php

use yii\db\Migration;

/**
 * Class m211215_145555_alter_exam_table_question_count_by_type
 */
class m211215_145555_alter_exam_table_question_count_by_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `question_count_by_type` varchar(255)  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211215_145555_alter_exam_table_question_count_by_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211215_145555_alter_exam_table_question_count_by_type cannot be reverted.\n";

        return false;
    }
    */
}
