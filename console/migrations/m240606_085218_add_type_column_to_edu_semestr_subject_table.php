<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%edu_semestr_subject}}`.
 */
class m240606_085218_add_type_column_to_edu_semestr_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('edu_semestr_subject' , 'type' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
