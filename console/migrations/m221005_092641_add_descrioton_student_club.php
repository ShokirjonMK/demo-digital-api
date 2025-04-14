<?php

use yii\db\Migration;

/**
 * Class m221005_092641_add_descrioton_student_club
 */
class m221005_092641_add_descrioton_student_club extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student_club', 'description', $this->text()->null()->after('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221005_092641_add_descrioton_student_club cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221005_092641_add_descrioton_student_club cannot be reverted.\n";

        return false;
    }
    */
}
