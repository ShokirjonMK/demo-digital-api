<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%option}}`.
 */
class m230804_071108_create_option_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'option';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('option');
        }

        $this->createTable('{{%option}}', [
            'id' => $this->primaryKey(),
            'test_id' => $this->integer()->notNull(),
            'text' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
            'file' => $this->string(100)->null(),
            'is_correct' => $this->tinyInteger(1)->notNull()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_option_table_test_table', 'option', 'test_id', 'test', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_option_table_test_table', 'option');
        $this->dropTable('{{%option}}');
    }
}
