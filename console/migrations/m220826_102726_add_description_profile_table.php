<?php

use yii\db\Migration;

/**
 * Class m220826_102726_add_description_profile_table
 */
class m220826_102726_add_description_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('profile', 'description', $this->text()->null()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220826_102726_add_description_profile_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220826_102726_add_description_profile_table cannot be reverted.\n";

        return false;
    }
    */
}
