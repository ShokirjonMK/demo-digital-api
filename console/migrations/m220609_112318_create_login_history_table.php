<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%login_history}}`.
 */
class m220609_112318_create_login_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'login_history';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('login_history');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%login_history}}', [
            'id' => $this->primaryKey(),

            'ip' => $this->string(255)->Null(),
            'user_id' => $this->integer()->Null(),
            'log_in_out' => $this->tinyInteger(1)->defaultValue(1),
            'device' => $this->string(255)->Null(),
            'device_id' => $this->string(255)->Null(),
            'type' => $this->string(255)->Null(),
            'model_device' => $this->string(255)->Null(),
            'data' => $this->text()->null(),
            'host' => $this->text()->null(),
            'ip_data' => $this->text()->null(),

            'created_on' => $this->dateTime()->Null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->Null()->defaultValue(0),
            'updated_by' => $this->integer()->Null()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%login_history}}');
    }
}
