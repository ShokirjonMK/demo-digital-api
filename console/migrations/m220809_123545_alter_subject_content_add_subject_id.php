<?php

use yii\db\Migration;

/**
 * Class m220809_123545_alter_subject_content_add_subject_id
 */
class m220809_123545_alter_subject_content_add_subject_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('subject_content', 'subject_id', $this->integer(1)->after('id'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_123545_alter_subject_content_add_subject_id cannot be reverted.\n";

        return false;
    }

 
}
