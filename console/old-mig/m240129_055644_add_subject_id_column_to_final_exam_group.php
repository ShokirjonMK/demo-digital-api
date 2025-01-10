<?php

use yii\db\Migration;

/**
 * Class m240129_055644_add_subject_id_column_to_final_exam_group
 */
class m240129_055644_add_subject_id_column_to_final_exam_group extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('final_exam_group' ,'subject_id' , $this->integer()->null());
        $this->addForeignKey('mk_final_exam_group_table_subject_table', 'final_exam_group', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240129_055644_add_subject_id_column_to_final_exam_group cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240129_055644_add_subject_id_column_to_final_exam_group cannot be reverted.\n";

        return false;
    }
    */
}
