<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%faculty}}`.
 */
class m240219_053326_add_dean_deputy_user_id_column_to_faculty_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('faculty' , 'dean_deputy_user_id' , $this->integer()->null());
        $this->addForeignKey('mk_faculty_table_dean_deputy_user_table', 'faculty', 'dean_deputy_user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
