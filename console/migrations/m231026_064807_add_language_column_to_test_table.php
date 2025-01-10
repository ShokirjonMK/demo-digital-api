<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%test}}`.
 */
class m231026_064807_add_language_column_to_test_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('test' ,'language_id' , $this->integer()->null());
        $this->addForeignKey('mk_test_table_language_table', 'test', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
