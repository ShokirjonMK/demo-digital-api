<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%attend}}`.
 */
class m231121_071339_add_group_id_column_to_attend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('attend' , 'group_id', $this->integer()->null());
        $this->addForeignKey('mk_attend_table_group_table', 'attend', 'group_id', 'group', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
