<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%subject_content}}`.
 */
class m241129_054644_add_main_column_to_subject_content_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_content' , 'main' , $this->tinyInteger(1)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
