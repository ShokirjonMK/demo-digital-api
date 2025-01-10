<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable_date}}`.
 */
class m240217_145646_add_attend_status_column_to_timetable_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable_date' , 'attend_status' , $this->tinyInteger(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
