<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_languages}}`.
 */
class m211018_162656_create_alter_languages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        // $this->execute("ALTER TABLE `languages` ADD `is_deleted` INT NULL DEFAULT '0' AFTER `updated_by`;");
        $this->addColumn('languages', 'is_deleted', $this->integer()->notNull()->defaultValue(0));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_languages}}');
    }
}
