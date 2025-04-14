<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%action_log}}`.
 */
class m220512_112417_create_action_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'action_log';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('action_log');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%action_log}}', [
            'id' => $this->primaryKey(),
            'controller' => $this->string(255)->Null(),
            'action' => $this->string(255)->Null(),
            'method' => $this->string(255)->Null(),
            'user_id' => $this->integer()->Null(),
            'data' => $this->text()->null(),
            'get_data' => $this->text()->null(),
            'post_data' => $this->text()->null(),
            'message' => $this->string(255)->null(),
            'browser' => $this->text()->null(),
            'ip_address' => $this->string(33)->null(),
            'result' => $this->text()->null(),
            'errors' => $this->text()->null(),

            'host' => $this->text()->null(),
            'ip_address_data' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'created_on' => $this->dateTime()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%action_log}}');
    }
}
