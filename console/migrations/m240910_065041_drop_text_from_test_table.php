<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%text_from_test}}`.
 */
class m240910_065041_drop_text_from_test_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('test', 'text');
        $this->dropColumn('test', 'file');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

    }
}
