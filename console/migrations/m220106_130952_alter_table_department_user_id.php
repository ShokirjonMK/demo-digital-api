<?php

use yii\db\Migration;

/**
 * Class m220106_130952_alter_table_department_user_id
 */
class m220106_130952_alter_table_department_user_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

         $this->execute("ALTER TABLE `department` ADD `user_id` INT(11)  NULL COMMENT 'Lead of department' ;");

        $this->addForeignKey('fu_department_user_id', 'department', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fu_department_user_id', 'department');

        echo "m220106_130952_alter_table_department_user_id cannot be reverted.\n";

        return false;
    }


}
