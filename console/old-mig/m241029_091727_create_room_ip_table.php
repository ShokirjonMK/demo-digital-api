<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%room_ip}}`.
 */
class m241029_091727_create_room_ip_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'room_ip';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('room_ip');
        }

        $this->createTable('{{%room_ip}}', [
            'id' => $this->primaryKey(),
            'room_id' => $this->integer()->notNull(),
            'ip' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_room_ip_table_room_table', 'room_ip', 'room_id', 'room', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%room_ip}}');
    }
}
