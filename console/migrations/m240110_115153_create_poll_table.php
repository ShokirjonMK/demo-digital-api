<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%poll}}`.
 */
class m240110_115153_create_poll_table extends Migration
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
        
        $tableName = Yii::$app->db->tablePrefix . 'poll';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('poll');
        }

        $this->createTable('{{%poll}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->defaultValue(1),
            'roles' => $this->json()->null(),
            'order' => $this->tinyInteger()->defaultValue(1),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->defaultValue(0),
            'updated_by' => $this->integer()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%poll}}');
    }
}
