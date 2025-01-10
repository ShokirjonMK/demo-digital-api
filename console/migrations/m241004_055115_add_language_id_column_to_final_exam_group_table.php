<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%final_exam_group}}`.
 */
class m241004_055115_add_language_id_column_to_final_exam_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('final_exam' , 'lang_id' , $this->integer()->null());
        $this->addForeignKey('mk_final_exam_table_lang_table', 'final_exam', 'lang_id', 'language', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
