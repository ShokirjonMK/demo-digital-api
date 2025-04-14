<?php

use yii\db\Migration;

/**
 * Class m220815_065959_alter_exam_table_add_old_exam_id
 */
class m220815_065959_alter_exam_table_add_old_exam_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam', 'old_exam_id', $this->integer()->after('id')->Null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220815_065959_alter_exam_table_add_old_exam_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220815_065959_alter_exam_table_add_old_exam_id cannot be reverted.\n";

        return false;
    }
    */
}
