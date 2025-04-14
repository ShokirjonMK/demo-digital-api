<?php

use yii\db\Migration;

/**
 * Class m220120_103947_atler_student_table_add_tutor_id
 */
class m220120_103947_atler_student_table_add_tutor_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `tutor_id` INT(11) NULL COMMENT 'tutor' ;");

        $this->addForeignKey('us_student_tutor_id', 'student', 'tutor_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('us_student_tutor_id', 'student');

        echo "m220120_103947_atler_student_table_add_tutor_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220120_103947_atler_student_table_add_tutor_id cannot be reverted.\n";

        return false;
    }
    */
}
