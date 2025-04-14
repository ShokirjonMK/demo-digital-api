<?php

use yii\db\Migration;

/**
 * Class m211109_055428_add_capacity_to_rooms_table
 */
class m211109_055428_add_capacity_to_rooms_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `room` add  `capacity` INT(11) NOT NULL DEFAULT(30);");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211109_055428_add_capacity_to_rooms_table cannot be reverted.\n";

        return false;
    }


}
