<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%mark_history}}`.
 */
class m240529_100932_create_mark_history_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'mark_history';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('mark_history');
        }

        $this->createTable('{{%mark_history}}', [
            'id' => $this->primaryKey(),
            'student_mark_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'ball' => $this->string()->null(),
            'update_time' => $this->integer()->null(),
            'ip' => $this->string()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%mark_history}}');
    }
}
