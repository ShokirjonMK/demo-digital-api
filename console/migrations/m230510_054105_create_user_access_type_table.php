<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access_type}}`.
 */
class m230510_054105_create_user_access_type_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'user_access_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('user_access_type');
        }

        $this->createTable('{{%user_access_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'url' => $this->string(255)->notNull(),
            'table_name' => $this->string(255)->notNull(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('user_access_type', [
            'id' => 1,
            'name' => 'Faculty',
            'url' => 'faculties',
            'table_name' => '\\common\\models\\model\\Faculty',
            'order' => 1,
            'status' => 1,
            'created_at' => 1,
            'updated_at' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'is_deleted' => 0,
        ]);

        $this->insert('user_access_type', [
            'id' => 2,
            'name' => 'Kafedra',
            'url' => 'kafedras',
            'table_name' => '\\common\\models\\model\\Kafedra',
            'order' => 1,
            'status' => 1,
            'created_at' => 1,
            'updated_at' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'is_deleted' => 0,
        ]);

        $this->insert('user_access_type', [
            'id' => 3,
            'name' => 'Department',
            'url' => 'departments',
            'table_name' => '\\common\\models\\model\\Department',
            'order' => 1,
            'status' => 1,
            'created_at' => 1,
            'updated_at' => 1,
            'created_by' => 1,
            'updated_by' => 1,
            'is_deleted' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%user_access_type}}');
    }
}
