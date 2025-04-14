<?php

use yii\db\Migration;

/**
 * Class m211221_152130_alter_exam_student_add_lang_id
 */
class m211221_152130_alter_exam_student_add_lang_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `lang_id` INT(11)  NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_152130_alter_exam_student_add_lang_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_152130_alter_exam_student_add_lang_id cannot be reverted.\n";

        return false;
    }
    */
}
