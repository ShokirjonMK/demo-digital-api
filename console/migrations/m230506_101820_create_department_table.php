<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%department}}`.
 */
class m230506_101820_create_department_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'department';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('department');
        }

        $this->createTable('{{%department}}', [
            'id' => $this->primaryKey(),
            'parent_id' => $this->integer()->null(),
            'type' => $this->tinyInteger()->null(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->Null()->comment('Lead of department'),
        ], $tableOptions);
        $this->addForeignKey('mk_department_table_users_table', 'department', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_department_table_users_table', 'department');
        $this->dropTable('{{%department}}');
    }
}
