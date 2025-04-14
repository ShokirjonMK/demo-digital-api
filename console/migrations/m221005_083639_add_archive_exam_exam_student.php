<?php

use yii\db\Migration;

/**
 * Class m221005_083639_add_archive_exam_exam_student
 */
class m221005_083639_add_archive_exam_exam_student extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam', 'archived', $this->integer()->defaultValue(0)->after('id'));
        $this->addColumn('exam_student', 'archived', $this->integer()->defaultValue(0)->after('id'));
        $this->addColumn('exam_student_answer', 'archived', $this->integer()->defaultValue(0)->after('id'));
        $this->addColumn('exam_student_answer_sub_question', 'archived', $this->integer()->defaultValue(0)->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221005_083639_add_archive_exam_exam_student cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221005_083639_add_archive_exam_exam_student cannot be reverted.\n";

        return false;
    }
    */
}
