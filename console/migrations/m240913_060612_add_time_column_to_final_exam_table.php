<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%final_exam}}`.
 */
class m240913_060612_add_time_column_to_final_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('final_exam' , 'start_time' , $this->integer()->null());
        $this->addColumn('final_exam' , 'finish_time' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
