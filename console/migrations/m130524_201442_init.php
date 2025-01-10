<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'users';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('users');
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'access_token' => $this->string(100)->defaultValue(null),
            'access_token_time' => $this->integer()->null(),
            'email' => $this->string()->null()->unique(),
            'template' => $this->string(255)->Null(),
            'layout' => $this->string(255)->Null(),
            'view' => $this->string(255)->Null(),
            'meta' => $this->json(),
            'status' => $this->smallInteger()->null(),
            'status_n' => $this->smallInteger()->notNull()->defaultValue(10),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cacheable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'searchable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_changed' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->insert('{{%users}}', [
            'username' => 'ShokirjonMK',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("12300123"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'mkshokirjon@gmail.com',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => '10ikbol16',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ik10002"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'ikboljon@gmail.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'ahror',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ahroruz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'a-user@asd.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'azizxon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("azizxonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'azizxon@asd.uz',
            'template' => '',
            'layout' => '',
            'view' => '',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}
