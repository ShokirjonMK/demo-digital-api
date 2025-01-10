<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable_date}}`.
 */
class m240402_060934_add_para_id_column_to_timetable_date_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable_attend' , 'para_id' , $this->integer()->null());
        $this->addForeignKey('mk_timetable_attend_table_para_table', 'timetable_attend', 'para_id', 'para', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
