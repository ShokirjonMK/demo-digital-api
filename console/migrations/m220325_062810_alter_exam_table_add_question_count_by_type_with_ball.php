<?php

use yii\db\Migration;

/**
 * Class m220325_062810_alter_exam_table_add_question_count_by_type_with_ball
 */
class m220325_062810_alter_exam_table_add_question_count_by_type_with_ball extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `question_count_by_type_with_ball` text  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220325_062810_alter_exam_table_add_question_count_by_type_with_ball cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220325_062810_alter_exam_table_add_question_count_by_type_with_ball cannot be reverted.\n";

        return false;
    }
    */
}
