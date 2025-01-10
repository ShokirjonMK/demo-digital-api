<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%topic}}`.
 */
class m241126_063233_add_hour_column_to_topic_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_topic' , 'hours' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
