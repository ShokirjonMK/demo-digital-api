<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%club_time}}`.
 */
class m220930_054919_create_club_time_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'club_time';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('club_time');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%club_time}}', [
            'id' => $this->primaryKey(),

            'club_category_id' => $this->integer()->null(),
            'club_id' => $this->integer()->notNull(),
            'room_id' => $this->integer()->null(),
            'building_id' => $this->integer()->null(),

            'times' => $this->json()->Null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('ctcc_club_time_club_category_id', 'club_time', 'club_category_id', 'club_category', 'id');
        $this->addForeignKey('ctc_club_time_club_id', 'club_time', 'club_id', 'club', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ctcc_club_time_club_category_id', 'club_time');
        $this->dropForeignKey('ctc_club_time_club_id', 'club_time');
        $this->dropTable('{{%club_time}}');
    }
}
