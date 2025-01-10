<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%profile}}`.
 */
class m240430_044355_add_parents_columns_to_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('profile' , 'father_fio' , $this->string(255)->null());
        $this->addColumn('profile' , 'father_number' , $this->string(255)->null());
        $this->addColumn('profile' , 'father_info' , $this->text()->null());
        $this->addColumn('profile' , 'mather_fio' , $this->string(255)->null());
        $this->addColumn('profile' , 'mather_number' , $this->string(255)->null());
        $this->addColumn('profile' , 'mather_info' , $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
