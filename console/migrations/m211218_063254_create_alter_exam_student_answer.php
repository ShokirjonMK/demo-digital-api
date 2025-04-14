<?php

use yii\db\Migration;

/**
 * Class m211218_063254_create_alter_exam_student_answer
 */
class m211218_063254_create_alter_exam_student_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_answer` ADD `parent_id` INT(11)  NULL  AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211218_063254_create_alter_exam_student_answer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211218_063254_create_alter_exam_student_answer cannot be reverted.\n";

        return false;
    }
    */
}
