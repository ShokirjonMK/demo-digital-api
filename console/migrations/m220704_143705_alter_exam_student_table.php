<?php

use yii\db\Migration;

/**
 * Class m220704_143705_alter_exam_student_table
 */
class m220704_143705_alter_exam_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `in_ball` double null  COMMENT 'oraliq bal';");
        $this->execute("ALTER TABLE `exam_student` ADD `is_checked` int default(0) COMMENT 'tekshirilganligi';");
        $this->execute("ALTER TABLE `exam_student` ADD `subject_id` int default(0) COMMENT 'fan id';");
        $this->execute("ALTER TABLE `exam_student` ADD `edu_semestr_subject_id` int default(0) COMMENT 'asdfasdasd';");
        $this->execute("ALTER TABLE `exam_student` ADD `is_checked_full` int default(0) COMMENT 'toliq tekshirilhanligi';");
        $this->execute("ALTER TABLE `exam_student` ADD `has_answer` int default(0) COMMENT 'javob yozilganligi';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220704_143705_alter_exam_student_table cannot be reverted.\n";

        return false;
    }
}
