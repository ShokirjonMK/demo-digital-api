<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subject_semestr}}`.
 */
class m241126_065206_add_name_column_to_subject_semestr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_semestr' , 'name', $this->string()->null());
        $this->addColumn('subject_semestr' , 'description', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
