<?php

use yii\db\Migration;

/**
 * Class m220122_050007_alter_edu_semestr_subject_table
 */
class m220122_050007_alter_edu_semestr_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_semestr_subject` ADD `faculty_id` INT(11) NULL COMMENT 'faculty' ;");
        $this->addForeignKey('edu_semestr_subject_faculty_id_bm', 'edu_semestr_subject', 'faculty_id', 'faculty', 'id');

        $this->execute("ALTER TABLE `edu_semestr_subject` ADD `direction_id` INT(11)  NULL COMMENT 'direction' ;");
        $this->addForeignKey('edu_semestr_subject_direction_id_bm', 'exam', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('edu_semestr_subject_faculty_id_bm', 'edu_semestr_subject');
        $this->dropForeignKey('edu_semestr_subject_direction_id_bm', 'edu_semestr_subject');
        echo "m220122_050007_alter_edu_semestr_subject_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220122_050007_alter_edu_semestr_subject_table cannot be reverted.\n";

        return false;
    }
    */
}
