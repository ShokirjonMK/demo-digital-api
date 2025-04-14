<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_supervisor}}`.
 */
class m240402_094821_create_exam_supervisor_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_supervisor';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_supervisor');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $this->createTable(
            '{{%exam_supervisor}}',
            [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'exam_id' => $this->integer()->notNull(),
                'room_id' => $this->integer()->null(),
                'building_id' => $this->integer()->null(),
                'start' => $this->dateTime()->null(),
                'end' => $this->dateTime()->null(),

                'order' => $this->tinyInteger(1)->defaultValue(1),
                'status' => $this->tinyInteger(1)->defaultValue(1),
                'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
                'created_at' => $this->integer()->notNull()->defaultValue(0),
                'updated_at' => $this->integer()->notNull()->defaultValue(0),
                'created_by' => $this->integer()->notNull()->defaultValue(0),
                'updated_by' => $this->integer()->notNull()->defaultValue(0),
                'archived' => $this->integer()->notNull()->defaultValue(0),
            ],
            $tableOptions
        );

        $this->addForeignKey('esu_exam_supervisor_user', 'exam_supervisor', 'user_id', 'users', 'id');
        $this->addForeignKey('esu_exam_supervisor_exam_id', 'exam_supervisor', 'exam_id', 'exam', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('esu_exam_supervisor_user', 'exam_supervisor');
        $this->dropForeignKey('esu_exam_supervisor_exam_id', 'exam_supervisor');
        $this->dropTable('{{%exam_supervisor}}');
    }
}
