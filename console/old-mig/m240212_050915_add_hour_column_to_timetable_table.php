<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable}}`.
 */
class m240212_050915_add_hour_column_to_timetable_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable' , 'hour' , $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
