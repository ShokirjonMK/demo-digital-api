<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%timetable_attend}}`.
 */
class m240312_092021_add_subject_category_id_column_to_timetable_attend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('timetable_attend' , 'subject_category_id' , $this->integer()->null());
        $this->addForeignKey('mk_timetable_attend_table_subject_category_table', 'timetable_attend', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
