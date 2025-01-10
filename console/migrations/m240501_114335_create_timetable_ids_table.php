<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable_ids}}`.
 */
class m240501_114335_create_timetable_ids_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable_ids';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable_ids');
        }

        $this->createTable('{{%timetable_ids}}', [
            'id' => $this->primaryKey(),
            'number' => $this->integer()->null()->unique()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%timetable_ids}}');
    }
}
