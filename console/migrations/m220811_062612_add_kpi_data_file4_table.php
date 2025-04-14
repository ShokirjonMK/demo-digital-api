<?php

use yii\db\Migration;

/**
 * Class m220811_062612_add_kpi_data_file4_table
 */
class m220811_062612_add_kpi_data_file4_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kpi_data', 'file4', $this->string(255)->after('file3')->Null());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220811_062612_add_kpi_data_file4_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220811_062612_add_kpi_data_file4_table cannot be reverted.\n";

        return false;
    }
    */
}
