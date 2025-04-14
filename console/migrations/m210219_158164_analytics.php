<?php

use yii\db\Migration;

/**
 * Class m210219_158164_analytics
 */
class m210219_158164_analytics extends Migration
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

        // Analytics sessions
        $this->createTable('{{%analytics_sessions}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(255),
            'session_key' => $this->string(255),
            'created_on' => $this->timestamp()->defaultValue(null),
            'updated_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        // Analytics users
        $this->createTable('{{%analytics_users}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(255),
            'ip_address' => $this->string(255),
            'country_code' => $this->string(255),
            'uagent' => $this->string(255),
            'ua_device' => $this->string(255),
            'ua_os' => $this->string(255),
            'ua_browser' => $this->string(255),
            'created_on' => $this->timestamp()->defaultValue(null),
            'updated_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);

        // Analytics views
        $this->createTable('{{%analytics_views}}', [
            'id' => $this->primaryKey(),
            'uid' => $this->string(255),
            'type' => $this->string(255),
            'value' => $this->string(255),
            'referrer' => $this->string(255),
            'status_code' => $this->integer()->notNull()->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%analytics_sessions}}');
        $this->dropTable('{{%analytics_users}}');
        $this->dropTable('{{%analytics_views}}');
    }
}
