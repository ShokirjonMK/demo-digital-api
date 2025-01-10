<?php

use yii\db\Migration;

/**
 * Class m240122_110644_add_passed_and_attend_column_student_mark_table
 */
class m240122_110644_add_passed_and_attend_column_student_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_mark' , 'passed' , $this->integer()->null());
        $this->addColumn('student_mark' , 'attend' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240122_110644_add_passed_and_attend_column_student_mark_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240122_110644_add_passed_and_attend_column_student_mark_table cannot be reverted.\n";

        return false;
    }
    */
}
