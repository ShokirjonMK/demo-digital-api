<?php

use yii\db\Migration;

/**
 * Class m220722_042749_alter_examStudentAnswer_table_add_appeal_conclution
 */
class m220722_042749_alter_examStudentAnswer_table_add_appeal_conclution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer` ADD `appeal_teacher_conclusion` text null COMMENT 'appeal xulosa ';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220722_042749_alter_examStudentAnswer_table_add_appeal_conclution cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220722_042749_alter_examStudentAnswer_table_add_appeal_conclution cannot be reverted.\n";

        return false;
    }
    */
}
