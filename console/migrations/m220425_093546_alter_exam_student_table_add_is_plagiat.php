<?php

use yii\db\Migration;

/**
 * Class m220425_093546_alter_exam_student_table_add_is_plagiat
 */
class m220425_093546_alter_exam_student_table_add_is_plagiat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `is_plagiat` int default(0) COMMENT '0-plagiat emas, 1-plagiat';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220425_093546_alter_exam_student_table_add_is_plagiat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_093546_alter_exam_student_table_add_is_plagiat cannot be reverted.\n";

        return false;
    }
    */
}
