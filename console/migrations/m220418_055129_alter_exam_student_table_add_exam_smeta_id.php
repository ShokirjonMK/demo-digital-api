<?php

use yii\db\Migration;

/**
 * Class m220418_055129_alter_exam_student_table_add_exam_smeta_id
 */
class m220418_055129_alter_exam_student_table_add_exam_smeta_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `exam_semeta_id` int NULL COMMENT 'exam_semeta id';");

        $this->addForeignKey('eses_exam_student_exam_exam_semeta_id', 'exam_student', 'exam_semeta_id', 'exam_semeta', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('eses_exam_student_exam_exam_semeta_id', 'exam_student');
        echo "m220418_055129_alter_exam_student_table_add_exam_smeta_id cannot be reverted.\n";

        return false;
    }

 
}
