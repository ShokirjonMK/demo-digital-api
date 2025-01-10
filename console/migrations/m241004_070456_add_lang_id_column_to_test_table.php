<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test}}`.
 */
class m241004_070456_add_lang_id_column_to_test_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('test' , 'lang_id' , $this->integer()->null());
        $this->addForeignKey('mk_test_table_lang_table', 'test', 'lang_id', 'language', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
