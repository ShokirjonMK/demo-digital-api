<?php

use yii\db\Migration;

/**
 * Class m211223_150739_alter_exam_table_duration
 */
class m211223_150739_alter_exam_table_duration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam` ADD `duration` INT(11)  NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_150739_alter_exam_table_duration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211223_150739_alter_exam_table_duration cannot be reverted.\n";

        return false;
    }
    */
}
