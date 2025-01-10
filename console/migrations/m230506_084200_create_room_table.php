<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%room}}`.
 */
class m230506_084200_create_room_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'room_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('room_type');
        }

        $tableName = Yii::$app->db->tablePrefix . 'room';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('room');
        }

        $this->createTable('{{%room_type}}', [
            'id' => $this->primaryKey(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('{{%room}}', [
            'id' => $this->primaryKey(),
            'building_id' => $this->integer()->notNull(),
            'capacity' => $this->integer()->notNull(),
            'room_size' => $this->float()->null(),
            'room_type_id'=>$this->integer()->null()->comment('1-Maruza xonasi, 2-Seminar xonasi, 3-Xodim xonasi, 4-qoshimcha xona'),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_room_table_building_table', 'room', 'building_id', 'building', 'id');
        $this->addForeignKey('mk_room_table_room_type_table', 'room', 'room_type_id', 'room_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_room_table_building_table', 'room');
        $this->dropTable('{{%room}}');
    }
}
