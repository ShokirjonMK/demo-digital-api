<?php

use yii\db\Migration;

/**
 * Class m220406_101515_residence_status_table
 */
class m220406_101515_residence_status_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'residence_status';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('residence_status');
        }

        $this->createTable('{{%residence_status}}', [
            'id' => $this->primaryKey(),
//            'name'=>$this->string(255)->notNull(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('residence_status');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220406_101515_residence_status_table cannot be reverted.\n";

        return false;
    }
    */
}
