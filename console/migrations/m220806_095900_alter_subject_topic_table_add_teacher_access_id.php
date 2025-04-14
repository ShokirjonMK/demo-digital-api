<?php

use yii\db\Migration;

/**
 * Class m220806_095900_alter_subject_topic_table_add_teacher_access_id
 */
class m220806_095900_alter_subject_topic_table_add_teacher_access_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_topic', 'teacher_access_id', $this->integer(1)->after('id'));

        $this->addForeignKey('scts_subject_topic_teacher_access_id', 'subject_topic', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220806_095900_alter_subject_topic_table_add_teacher_access_id cannot be reverted.\n";
        $this->dropForeignKey('scts_subject_topic_teacher_access_id', 'subject_topic');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220806_095900_alter_subject_topic_table_add_teacher_access_id cannot be reverted.\n";

        return false;
    }
    */
}
