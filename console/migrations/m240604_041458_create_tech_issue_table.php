<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tech_issue}}`.
 */
class m240604_041458_create_tech_issue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'tech_issue';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('tech_issue');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tech_issue}}', [
            'id' => $this->primaryKey(),
            'tech_issue_type_id' => $this->integer()->null(),
            'building_id' => $this->integer()->null(),
            'room_id' => $this->integer()->null(),


            'issue_user_id' => $this->integer()->null(),
            'answer_user_id' => $this->integer()->null(),

            'issue_text' => $this->text()->null(),
            'answer_text' => $this->text()->null(),
            'file' => $this->string()->null(),
            'issue_file' => $this->string()->null(),
            'answer_file' => $this->string()->null(),

            'answer_date' => $this->date()->null(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(0)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0)
        ]);


        $this->addForeignKey('ti_tech_issue_issue_user_id', 'tech_issue', 'issue_user_id', 'users', 'id');
        $this->addForeignKey('ti_tech_issue_answer_user_id', 'tech_issue', 'answer_user_id', 'users', 'id');
        $this->addForeignKey('ti_tech_issue_tech_issue_type_id', 'tech_issue', 'tech_issue_type_id', 'tech_issue_type', 'id');
        $this->addForeignKey('ti_tech_issue_building_id', 'tech_issue', 'building_id', 'building', 'id');
        $this->addForeignKey('ti_tech_issue_room_id', 'tech_issue', 'room_id', 'room', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ti_tech_issue_room_id', 'tech_issue');
        $this->dropForeignKey('ti_tech_issue_issue_user_id', 'tech_issue');
        $this->dropForeignKey('ti_tech_issue_answer_user_id', 'tech_issue');
        $this->dropForeignKey('ti_tech_issue_building_id', 'tech_issue');
        $this->dropForeignKey('ti_tech_issue_tech_issue_type_id', 'tech_issue');


        $this->dropTable('{{%tech_issue}}');
    }
}
