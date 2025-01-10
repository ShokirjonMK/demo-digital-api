<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subject}}`.
 */
class m241109_091350_add_minutes_column_to_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject' , 'question_count' , $this->integer()->defaultValue(20));
        $this->addColumn('subject' , 'all_time' , $this->integer()->defaultValue(40));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
