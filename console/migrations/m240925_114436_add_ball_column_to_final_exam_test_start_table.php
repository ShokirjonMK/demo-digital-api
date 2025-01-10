<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%final_exam_test_start}}`.
 */
class m240925_114436_add_ball_column_to_final_exam_test_start_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('final_exam_test_start' , 'ball' , $this->float()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
