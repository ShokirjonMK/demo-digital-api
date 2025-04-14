<?php

use yii\db\Migration;

/**
 * Class m220127_073147_alter_subject_table_add_parent_id
 */
class m220127_073147_alter_subject_table_add_parent_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `subject` ADD `parent_id` INT(11) NULL COMMENT 'fanga parent' ;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220127_073147_alter_subject_table_add_parent_id cannot be reverted.\n";

        return false;
    }
}
