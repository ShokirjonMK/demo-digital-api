<?php

use yii\db\Migration;

/**
 * Class m220728_103431_alter_exam_table_add_edu_year_id
 */
class m220728_103431_alter_exam_table_add_edu_year_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `exam_student` ADD `edu_year_id` int null COMMENT 'talim yili';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220728_103431_alter_exam_table_add_edu_year_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220728_103431_alter_exam_table_add_edu_year_id cannot be reverted.\n";

        return false;
    }
    */
}
