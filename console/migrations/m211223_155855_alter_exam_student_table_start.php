<?php

use yii\db\Migration;

/**
 * Class m211223_155855_alter_exam_student_table_start
 */
class m211223_155855_alter_exam_student_table_start extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `start` int  NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_155855_alter_exam_student_table_start cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211223_155855_alter_exam_student_table_start cannot be reverted.\n";

        return false;
    }
    */
}
