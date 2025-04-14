<?php

use yii\db\Migration;

/**
 * Class m220418_071513_alter_exam_student_table_add_conclusion_plagiat_file_and_plagiat_percent
 */
class m220418_071513_alter_exam_student_table_add_conclusion_plagiat_file_and_plagiat_percent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `conclusion` text NULL COMMENT 'umumiy xulosa';");
        $this->execute("ALTER TABLE `exam_student` ADD `plagiat_file` varchar(255) NULL COMMENT 'fayl';");
        $this->execute("ALTER TABLE `exam_student` ADD `plagiat_percent` float NULL COMMENT 'foyizi';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220418_071513_alter_exam_student_table_add_conclusion_plagiat_file_and_plagiat_percent cannot be reverted.\n";

        return false;
    }
}
