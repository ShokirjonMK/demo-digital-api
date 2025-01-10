<?php

use yii\db\Migration;

/**
 * Class m250104_113545_change_topic_id_nullable
 */
class m250104_113545_change_topic_id_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()



    {
        $this->alterColumn('{{%test}}', 'topic_id', $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250104_113545_change_topic_id_nullable cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250104_113545_change_topic_id_nullable cannot be reverted.\n";

        return false;
    }
    */
}
