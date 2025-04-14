<?php

use yii\db\Migration;

/**
 * Class m200622_043505_table_logs
 */
class m200622_043505_table_logs extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%logs_admin}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'res_id' => $this->integer(),
            'type' => $this->string(255),
            'action' => $this->string(255),
            'data' => 'LONGTEXT',
            'ip_address' => $this->string(255),
            'browser' => $this->text(),
            'created_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%logs_frontend}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'res_id' => $this->integer(),
            'type' => $this->string(255),
            'action' => $this->string(255),
            'data' => 'LONGTEXT',
            'ip_address' => $this->string(255),
            'browser' => $this->text(),
            'created_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        $this->createTable('{{%logs_seller}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'res_id' => $this->integer(),
            'type' => $this->string(255),
            'action' => $this->string(255),
            'data' => 'LONGTEXT',
            'ip_address' => $this->string(255),
            'browser' => $this->text(),
            'created_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%logs_admin}}');
        $this->dropTable('{{%logs_frontend}}');
        $this->dropTable('{{%logs_seller}}');
        $this->dropTable('{{%users_log}}');
    }
}
