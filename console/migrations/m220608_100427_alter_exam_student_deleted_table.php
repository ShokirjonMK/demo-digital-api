<?php

use yii\db\Migration;

/**
 * Class m220608_100427_alter_exam_student_deleted_table
 */
class m220608_100427_alter_exam_student_deleted_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student_deleted` ADD `created_at_o` int null COMMENT 'eski yaratilgani';");
        $this->execute("ALTER TABLE `exam_student_deleted` ADD `updated_at_o` int null COMMENT 'eski o`zgartirilgan';");
        $this->execute("ALTER TABLE `exam_student_deleted` ADD `created_by_o` int null COMMENT 'eski yaratilgani';");
        $this->execute("ALTER TABLE `exam_student_deleted` ADD `updated_by_o` int null COMMENT 'eski yaratilgani';");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_100427_alter_exam_student_deleted_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220608_100427_alter_exam_student_deleted_table cannot be reverted.\n";

        return false;
    }
    */
}
