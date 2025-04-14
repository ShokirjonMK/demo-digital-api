<?php

use yii\db\Migration;

/**
 * Class m211109_160302_alter_add_student_table
 */
class m211109_160302_alter_add_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` add  `edu_plan_id` INT(11)  NULL;");
        $this->addForeignKey('wk_student_edu_plan_id', 'student', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('wk_student_edu_plan_id', 'student');
        
        echo "m211109_160302_alter_add_student_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211109_160302_alter_add_student_table cannot be reverted.\n";

        return false;
    }
    */
}
