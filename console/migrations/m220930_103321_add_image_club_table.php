<?php

use yii\db\Migration;

/**
 * Class m220930_103321_add_image_club_table
 */
class m220930_103321_add_image_club_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('club', 'image', $this->string()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220930_103321_add_image_club_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220930_103321_add_image_club_table cannot be reverted.\n";

        return false;
    }
    */
}
