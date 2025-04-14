<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%alter_languages}}`.
 */
class m211018_162215_create_alter_languages_table extends Migration
{

    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $this->addColumn('languages', 'created_at', $this->integer());
        $this->addColumn('languages', 'updated_at', $this->integer());
        $this->addColumn('languages', 'created_by', $this->integer());
        $this->addColumn('languages', 'updated_by', $this->integer());

    //     $this->execute("ALTER TABLE 
    //     `languages` ADD `created_at` INT NULL AFTER `status`,
    //    ADD `updated_at` INT NULL AFTER `created_at`, 
    //    ADD `created_by` INT NULL AFTER `updated_at`, 
    //    ADD `updated_by` INT NULL AFTER `created_by`;
    //    ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%alter_languages}}');
    }
}
