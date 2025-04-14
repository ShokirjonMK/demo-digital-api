<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access}}`.
 */
class m220106_103125_create_user_access_table extends Migration
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
        $this->createTable('{{%user_access}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'is_leader' => $this->tinyInteger(1)->defaultValue(0),
            'user_access_type_id' => $this->integer()->notNull(),
            'table_id' => $this->integer()->notNull(),
            'table_name' => $this->string()->Null(),
            'role_name' => $this->string()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('rui_user_access_user', 'user_access', 'user_id', 'users', 'id');
        $this->addForeignKey('rui_user_access_user_access_type', 'user_access', 'user_access_type_id', 'user_access_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('rui_user_access_user', 'user_access');
        $this->dropForeignKey('rui_user_access_user_access_type', 'user_access');

        $this->dropTable('{{%user_access}}');
    }
}
