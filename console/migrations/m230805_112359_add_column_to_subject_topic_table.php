<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subject_topic}}`.
 */
class m230805_112359_add_column_to_subject_topic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
//        $this->addColumn('subject_topic', 'parent_id', $this->integer()->null());

//        $this->addForeignKey('mk_student_topic_table_parent_table', 'subject_topic', 'parent_id', 'subject_topic', 'id');

//        $this->addColumn('subject_topic', 'attempts_count', $this->integer()->null());
//        $this->addColumn('subject_topic', 'duration_reading_time', $this->integer()->null());
//        $this->addColumn('subject_topic', 'test_count', $this->integer()->defaultValue(0));
//        $this->addColumn('subject_topic', 'min_percentage', $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
