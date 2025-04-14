<?php

use yii\db\Migration;

/**
 * Class m211103_150431_add_year_attribute_to_edu_year_table
 */
class m211103_150431_add_year_attribute_to_edu_year_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_year` ADD `year` year NOT NULL UNIQUE AFTER `id`;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211103_150431_add_year_attribute_to_edu_year_table cannot be reverted.\n";

        return false;
    }

}
