<?php

use yii\db\Migration;

/**
 * Class m211025_123326_add_model_id_to_translate_table
 */
class m211025_123326_add_model_id_to_translate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `translate` ADD `model_id` INT NULL AFTER `id`;");
        $this->execute("ALTER TABLE `translate` ADD `description` TEXT NULL AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211025_123326_add_model_id_to_translate_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211025_123326_add_model_id_to_translate_table cannot be reverted.\n";

        return false;
    }
    */
}
