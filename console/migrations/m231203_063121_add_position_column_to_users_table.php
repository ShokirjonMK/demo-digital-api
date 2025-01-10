<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%users}}`.
 */
class m231203_063121_add_position_column_to_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('users' , 'position' , $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
