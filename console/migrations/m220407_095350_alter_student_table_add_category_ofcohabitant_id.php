<?php

use yii\db\Migration;

/**
 * Class m220407_095350_alter_student_table_add_category_ofcohabitant_id
 */
class m220407_095350_alter_student_table_add_category_ofcohabitant_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `student` ADD `category_of_cohabitant_id` int NULL COMMENT 'category_of_cohabitant ';");

        $this->addForeignKey('scofc_student_category_of_cohabitant_mk', 'student', 'category_of_cohabitant_id', 'category_of_cohabitant', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('scofc_student_category_of_cohabitant_mk', 'student');
        echo "m220407_095350_alter_student_table_add_category_ofcohabitant_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_095350_alter_student_table_add_category_ofcohabitant_id cannot be reverted.\n";

        return false;
    }
    */
}
