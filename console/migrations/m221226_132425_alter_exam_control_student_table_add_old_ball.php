<?php

use yii\db\Migration;

/**
 * Class m221226_132425_alter_exam_control_student_table_add_old_ball
 */
class m221226_132425_alter_exam_control_student_table_add_old_ball extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_control_student', 'old_ball', $this->double()->null()->after('ball'));
        $this->addColumn('exam_control_student', 'old_ball2', $this->double()->null()->after('ball2'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221226_132425_alter_exam_control_student_table_add_old_ball cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221226_132425_alter_exam_control_student_table_add_old_ball cannot be reverted.\n";

        return false;
    }
    */
}
