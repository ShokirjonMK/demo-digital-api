<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%load_rate}}`.
 */
class m230927_044620_create_load_rate_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'load_rate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('load_rate');
        }

        $this->createTable('{{%load_rate}}', [
            'id' => $this->primaryKey(),
            'user_access_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'work_load_id' => $this->integer()->null(),
            'work_rate_id' => $this->integer()->null(),

            'status' => $this->integer()->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ] , $tableOptions);

        $this->addForeignKey('mk_load_rate_table_user_access_table', 'load_rate', 'user_access_id', 'user_access', 'id');
        $this->addForeignKey('mk_load_rate_table_user_table', 'load_rate', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_load_rate_table_work_load_table', 'load_rate', 'work_load_id', 'work_load', 'id');
        $this->addForeignKey('mk_load_rate_table_work_rate_table', 'load_rate', 'work_rate_id', 'work_rate', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%load_rate}}');
    }
}
