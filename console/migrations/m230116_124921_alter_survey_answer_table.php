<?php

use yii\db\Migration;

/**
 * Class m230116_124921_alter_survey_answer_table
 */
class m230116_124921_alter_survey_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->addColumn('survey_answer', 'teacher_user_id', $this->integer()->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230116_124921_alter_survey_answer_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230116_124921_alter_survey_answer_table cannot be reverted.\n";

        return false;
    }
    */
}
