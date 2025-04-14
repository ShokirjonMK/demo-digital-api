<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_session}}`.
 */
class m200625_122720_create_users_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('users_session', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->defaultValue(0),
            'last_ip' => $this->string(100),
            'last_login' => $this->timestamp()->defaultValue(null),
            'last_session' => $this->json(),
            'history' => $this->json(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users_session');
    }
}
