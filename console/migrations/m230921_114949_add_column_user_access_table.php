<?php

use yii\db\Migration;

/**
 * Class m230921_114949_add_column_user_access_table
 */
class m230921_114949_add_column_user_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */

    public function safeUp()
    {
        $user = \common\models\model\UserAccess::find()->all();
        foreach ($user as $i) {
            $i->work_rate_id = 1;
            $i->work_load_id = 1;
            $i->save(false);
        }
        $this->addForeignKey('mk_user_access_table_work_load_table', 'user_access', 'work_load_id', 'work_load', 'id', 'CASCADE');
        $this->addForeignKey('mk_user_access_table_work_rate_table', 'user_access', 'work_rate_id', 'work_rate', 'id' , 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230921_114949_add_column_user_access_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230921_114949_add_column_user_access_table cannot be reverted.\n";

        return false;
    }
    */
}
