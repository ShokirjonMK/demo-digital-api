<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%user_role}}`.
 */
class m230909_044144_add_column_to_user_role_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'attach_role', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
