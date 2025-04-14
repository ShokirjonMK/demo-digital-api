<?php

use yii\db\Migration;

/**
 * Class m220708_071637_alter_exam_STUDENT_TABLE_add_on1_on2
 */
class m220708_071637_alter_exam_STUDENT_TABLE_add_on1_on2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `on1` double null COMMENT 'oraliq 1';");
        $this->execute("ALTER TABLE `exam_student` ADD `on2` double null COMMENT 'oraliq 2';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220708_071637_alter_exam_STUDENT_TABLE_add_on1_on2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220708_071637_alter_exam_STUDENT_TABLE_add_on1_on2 cannot be reverted.\n";

        return false;
    }
    */
}
