<?php

use yii\db\Migration;

/**
 * Class m220407_095425_alter_student_table_add_student_category_id
 */
class m220407_095425_alter_student_table_add_student_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `student_category_id` int NULL COMMENT 'student_category id ';");

        $this->addForeignKey('ssc_student_student_category_mk', 'student', 'student_category_id', 'student_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ssc_student_student_category_mk', 'student');
        echo "m220407_095425_alter_student_table_add_student_category_id cannot be reverted.\n";

        return false;
    }

}
