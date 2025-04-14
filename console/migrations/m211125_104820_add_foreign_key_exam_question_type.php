<?php

use yii\db\Migration;

/**
 * Class m211125_104820_add_foreign_key_exam_question_type
 */
class m211125_104820_add_foreign_key_exam_question_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('eqt_exam_question_exam_type_reletion_mk', 'exam_question', 'exam_question_type_id', 'exam_question_type', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eqt_exam_question_exam_type_reletion_mk', 'exam_question');
        echo "m211125_104820_add_foreign_key_exam_question_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211125_104820_add_foreign_key_exam_question_type cannot be reverted.\n";

        return false;
    }
    */
}
