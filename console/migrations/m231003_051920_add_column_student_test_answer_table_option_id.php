<?php

use yii\db\Migration;

/**
 * Class m231003_051920_add_column_student_test_answer_table_option_id
 */
class m231003_051920_add_column_student_test_answer_table_option_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_topic_test_answer', 'option_id', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231003_051920_add_column_student_test_answer_table_option_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231003_051920_add_column_student_test_answer_table_option_id cannot be reverted.\n";

        return false;
    }
    */
}
