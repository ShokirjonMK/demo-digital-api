<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_edu_plan}}`.
 */
class m211101_121010_create_alter_edu_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE 
        `edu_plan` 
        ADD `fall_start` date NULL AFTER `id`,
        ADD `fall_end` date NULL AFTER `id`,
        ADD `spring_start` date NULL AFTER `id`,
        ADD `spring_end` date NULL AFTER `id`;
       ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_edu_plan}}');
    }
}
