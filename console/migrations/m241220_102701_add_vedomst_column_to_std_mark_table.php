<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%std_mark}}`.
 */
class m241220_102701_add_vedomst_column_to_std_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_mark' , 'vedomst' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
