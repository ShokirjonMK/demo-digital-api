<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_access_type}}`.
 */
class m220106_102830_create_user_access_type_table extends Migration
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
        $this->createTable('{{%user_access_type}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
            'table_name' => $this->string()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('user_access_type', [
            'name' => 'Faculty',
            'url' => 'faculties',

            'table_name' => '\common\models\model\Faculty',
        ]);
        $this->insert('user_access_type', [
            'name' => 'Kafedra',
            'url' => 'kafedras',
            'table_name' => '\common\models\model\Kafedra',
        ]);
        $this->insert('user_access_type', [
            'name' => 'Department',
            'url' => 'departments',
            'table_name' => '\common\models\model\Department',
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
