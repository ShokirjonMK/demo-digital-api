<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%election_candidate_innfo}}`.
 */
class m220517_040815_create_election_candidate_innfo_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'election_candidate_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('election_candidate_info');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%election_candidate_info}}', [
            'id' => $this->primaryKey(),
            'election_candidate_id' => $this->integer()->notNull(),
            'lang' => $this->string(3)->null(),
            'photo' => $this->string(255)->null(),
            'short_info' => $this->text()->null(),
            'full_info' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('eciec_election_candidate_info_election_candidate_mk', 'election_candidate_info', 'election_candidate_id', 'election_candidate', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eciec_election_candidate_info_election_candidate_mk', 'election_candidate_info');
        $this->dropTable('{{%election_candidate_innfo}}');
    }
}
