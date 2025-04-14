<?php

use yii\db\Migration;

/**
 * Class m220627_102601_alter_exam_student_table_add_type
 */
class m220627_102601_alter_exam_student_table_add_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `type` int null COMMENT '1 ielts 2 nogiron masalan';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220627_102601_alter_exam_student_table_add_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220627_102601_alter_exam_student_table_add_type cannot be reverted.\n";

        return false;
    }
    */
}
