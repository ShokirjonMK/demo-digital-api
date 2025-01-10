<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%final_exam_group}}`.
 */
class m240913_063633_add_time_column_to_final_exam_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('final_exam_group' , 'start_time' , $this->integer()->null());
        $this->addColumn('final_exam_group' , 'finish_time' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
