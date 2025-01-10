<?php

use yii\db\Migration;

/**
 * Class m241204_081133_add_type_to_exam_type_table
 */
class m241204_081133_add_type_to_exam_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exams_type' , 'type' , $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241204_081133_add_type_to_exam_type_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241204_081133_add_type_to_exam_type_table cannot be reverted.\n";

        return false;
    }
    */
}
