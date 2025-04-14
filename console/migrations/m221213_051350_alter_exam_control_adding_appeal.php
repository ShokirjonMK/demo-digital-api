<?php

use yii\db\Migration;

/**
 * Class m221213_051350_alter_exam_control_adding_appeal
 */
class m221213_051350_alter_exam_control_adding_appeal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_control', 'appeal_at', $this->integer()->null()->after('status'));
        $this->addColumn('exam_control', 'appeal2_at', $this->integer()->null()->after('status'));
        $this->addColumn('exam_control', 'status2', $this->integer()->null()->after('status'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221213_051350_alter_exam_control_adding_appeal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221213_051350_alter_exam_control_adding_appeal cannot be reverted.\n";

        return false;
    }
    */
}
