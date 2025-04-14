<?php

use yii\db\Migration;

/**
 * Class m230519_103451_alter_exam_student_add_act_params
 */
class m230519_103451_alter_exam_student_add_act_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_student', 'act_file', $this->string()->null()->after('act'));
        $this->addColumn('exam_student', 'act_reason', $this->string()->null()->after('act'));
    }

    public function safeDown()
    {


        return false;
    }
}
