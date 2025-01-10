<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%student_group}}`.
 */
class m240408_041216_add_semestr_key_column_to_student_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('student_group' , 'semestr_key' , $this->string(255)->unique()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
