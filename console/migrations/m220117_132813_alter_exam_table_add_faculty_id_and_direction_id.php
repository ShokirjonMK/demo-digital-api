<?php

use yii\db\Migration;

/**
 * Class m220117_132813_alter_exam_table_add_faculty_id_and_direction_id
 */
class m220117_132813_alter_exam_table_add_faculty_id_and_direction_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `faculty_id` INT(11) NULL COMMENT 'faculty' ;");
        $this->addForeignKey('efmk_exam_faculty_id_mk', 'exam', 'faculty_id', 'faculty', 'id');

        $this->execute("ALTER TABLE `exam` ADD `direction_id` INT(11)  NULL COMMENT 'direction' ;");
        $this->addForeignKey('efmk_exam_direction_id_mk', 'exam', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('efmk_exam_faculty_id_mk', 'exam');
        $this->dropForeignKey('efmk_exam_direction_id_mk', 'exam');

        echo "m220117_132813_alter_exam_table_add_faculty_id_and_direction_id cannot be reverted.\n";

        return false;
    }
}
