<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable_date}}`.
 */
class m240214_073836_add_type_column_to_timetable_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable_date' , 'type' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
