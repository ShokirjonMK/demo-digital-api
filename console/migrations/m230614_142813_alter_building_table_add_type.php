<?php

use yii\db\Migration;

/**
 * Class m230614_142813_alter_building_table_add_type
 */
class m230614_142813_alter_building_table_add_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('building', 'type', $this->integer()->defaultValue(1)->after('id')->comment('type education building or hostel or something'));

        // $this->addColumn('building', 'type', $this->integer()->default(1)->after('id')->comment('type education building or hostel or something'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230614_142813_alter_building_table_add_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230614_142813_alter_building_table_add_type cannot be reverted.\n";

        return false;
    }
    */
}
