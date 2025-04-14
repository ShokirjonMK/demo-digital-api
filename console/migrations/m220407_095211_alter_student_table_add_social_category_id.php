<?php

use yii\db\Migration;

/**
 * Class m220407_095211_alter_student_table_add_social_category_id
 */
class m220407_095211_alter_student_table_add_social_category_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute("ALTER TABLE `student` ADD `social_category_id` int NULL COMMENT ' ijtimoiy toifa ';");

        $this->addForeignKey('ssc_student_social_category_mk', 'student', 'social_category_id', 'social_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ssc_student_social_category_mk', 'student');
        echo "m220407_095211_alter_student_table_add_social_category_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220407_095211_alter_student_table_add_social_category_id cannot be reverted.\n";

        return false;
    }
    */
}
