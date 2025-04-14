<?php

use yii\db\Migration;

/**
 * Class m220203_092322_alter_table_student_add_edu_form
 */
class m220203_092322_alter_table_student_add_edu_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `edu_form_id` int default 1 COMMENT 'talim shakli id ';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_092322_alter_table_student_add_edu_form cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_092322_alter_table_student_add_edu_form cannot be reverted.\n";

        return false;
    }
    */
}
