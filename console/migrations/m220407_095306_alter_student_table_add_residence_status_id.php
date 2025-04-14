<?php

use yii\db\Migration;

/**
 * Class m220407_095306_alter_student_table_add_residence_status_id
 */
class m220407_095306_alter_student_table_add_residence_status_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `residence_status_id` int NULL COMMENT 'category_of_cohabitant id ';");

        $this->addForeignKey('srs_student_residence_status_mk', 'student', 'residence_status_id', 'residence_status', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('srs_student_residence_status_mk', 'student');
        echo "m220407_095306_alter_student_table_add_residence_status_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_095306_alter_student_table_add_residence_status_id cannot be reverted.\n";

        return false;
    }
    */
}
