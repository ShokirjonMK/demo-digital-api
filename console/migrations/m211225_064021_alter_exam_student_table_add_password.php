<?php

use yii\db\Migration;

/**
 * Class m211225_064021_alter_exam_student_table_add_password
 */
class m211225_064021_alter_exam_student_table_add_password extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `password` varchar(33) null ;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211225_064021_alter_exam_student_table_add_password cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211225_064021_alter_exam_student_table_add_password cannot be reverted.\n";

        return false;
    }
    */
}
