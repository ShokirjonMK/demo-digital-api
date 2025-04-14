<?php

use yii\db\Migration;

/**
 * Class m230724_032341_alter_kpi_category_table_add_user_ids
 */
class m230724_032341_alter_kpi_category_table_add_user_ids extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('kpi_category', 'user_ids', $this->json()->null()->after('id')->comment('tekshiruvchi uselar id lari'));
        // $this->addColumn('kpi_category', 'user_ids', 'JSON NULL');

        
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('kpi_category', 'user_ids');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230724_032341_alter_kpi_category_table_add_user_ids cannot be reverted.\n";

        return false;
    }
    */
}
