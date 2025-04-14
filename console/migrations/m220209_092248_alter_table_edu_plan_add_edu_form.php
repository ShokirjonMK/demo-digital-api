<?php

use yii\db\Migration;

/**
 * Class m220209_092248_alter_table_edu_plan_add_edu_form
 */
class m220209_092248_alter_table_edu_plan_add_edu_form extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE `edu_plan` ADD `edu_form_id` int  NULL COMMENT 'ta-lim shakli';");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220209_092248_alter_table_edu_plan_add_edu_form cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220209_092248_alter_table_edu_plan_add_edu_form cannot be reverted.\n";

        return false;
    }
    */
}
