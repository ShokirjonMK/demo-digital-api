<?php

use yii\db\Migration;

/**
 * Class m231015_050924_add_column_test_table
 */
class m231015_050924_add_column_test_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('test' , 'subject_id' , $this->integer()->notNull());
        $this->addColumn('test' , 'exam_type_id' , $this->integer()->null());
        $this->addColumn('test' , 'is_checked' , $this->integer()->defaultValue(0));
        $this->addForeignKey('mk_test_table_subjectId_table', 'test', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_test_table_exams_type_table', 'test', 'exam_type_id', 'exams_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231015_050924_add_column_test_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231015_050924_add_column_test_table cannot be reverted.\n";

        return false;
    }
    */
}
