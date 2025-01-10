<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access}}`.
 */
class m230510_054116_create_user_access_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'user_access';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('user_access');
        }

        $this->createTable('{{%user_access}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'user_access_type_id' => $this->integer()->notNull(),
            'table_id' => $this->integer()->notNull(),
            'is_leader' => $this->tinyInteger()->Null()->defaultValue(0),
            'table_name' => $this->string(255)->Null(),
            'role_name' => $this->string(255)->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_user_access_table_user_access_type_table', 'user_access', 'user_access_type_id', 'user_access_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_user_access_table_user_access_type_table', 'user_access');
        $this->dropTable('{{%user_access}}');
    }
}
