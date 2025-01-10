<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%room_ip}}`.
 */
class m241030_061603_add_building_id_column_to_room_ip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('room_ip' , 'building_id' , $this->integer()->null());
        $this->addForeignKey('mk_room_ip_table_building_table', 'room_ip', 'building_id', 'building', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
