<?php

use yii\db\Migration;

/**
 * Class m220922_112420_user_add_created_updated_by
 */
class m220922_112420_user_add_created_updated_by extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('users', 'created_by', $this->integer()->null());
        $this->addColumn('users', 'updated_by', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220922_112420_user_add_created_updated_by cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220922_112420_user_add_created_updated_by cannot be reverted.\n";

        return false;
    }
    */
}
