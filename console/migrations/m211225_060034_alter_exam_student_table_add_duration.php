<?php

use yii\db\Migration;

/**
 * Class m211225_060034_alter_exam_student_table_add_duration
 */
class m211225_060034_alter_exam_student_table_add_duration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `duration` int null  default 0;");
        $this->execute("ALTER TABLE `exam_student` ADD `finish` int null ");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211225_060034_alter_exam_student_table_add_duration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211225_060034_alter_exam_student_table_add_duration cannot be reverted.\n";

        return false;
    }
    */
}
