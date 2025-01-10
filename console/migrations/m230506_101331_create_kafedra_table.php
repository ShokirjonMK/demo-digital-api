<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kafedra}}`.
 */
class m230506_101331_create_kafedra_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'kafedra';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kafedra');
        }

        $this->createTable('{{%kafedra}}', [
            'id' => $this->primaryKey(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->null(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'user_id' => $this->integer()->Null()->comment('Lead of kafedra or Mudir'),
        ], $tableOptions);
        $this->addForeignKey('mk_kafedra_table_faculty_table', 'kafedra', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_kafedra_table_direction_table', 'kafedra', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_kafedra_table_users_table', 'kafedra', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_kafedra_table_faculty_table', 'kafedra');
        $this->dropForeignKey('mk_kafedra_table_direction_table', 'kafedra');
        $this->dropForeignKey('mk_kafedra_table_users_table', 'kafedra');
        $this->dropTable('{{%kafedra}}');
    }
}
