<?php

use yii\db\Migration;

/**
 * Class m211108_142620_time_table_adding_edu_semester_id
 */
class m211108_142620_time_table_adding_edu_semester_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `time_table` add  `edu_semester_id` INT(11) NOT NULL;");
        $this->addForeignKey('wk_time_table_edu_semester_id', 'time_table', 'edu_semester_id', 'edu_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('wk_time_table_edu_semester_id', 'time_table');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211108_142620_time_table_adding_edu_semester_id cannot be reverted.\n";

        return false;
    }
    */
}
