<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable_attend}}`.
 */
class m240312_091534_add_subject__id_column_to_timetable_attend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable_attend' , 'subject_id' , $this->integer()->null());
        $this->addForeignKey('mk_timetable_attend_table_subject_table', 'timetable_attend', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
