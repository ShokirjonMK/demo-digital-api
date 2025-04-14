<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_conclution}}`.
 */
class m230605_060946_create_exam_conclution_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_conclution';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_conclution');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%exam_conclution}}', [
            'id' => $this->primaryKey(),

            'text' => $this->text()->null(),
            'key' => $this->string(33)->null(),
            'subject_id' => $this->integer(11)->null(),
            'lang_code' => $this->string(33)->null(),
            'language_id' => $this->integer()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->null()->defaultValue(0),
            'updated_by' => $this->integer()->null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->null()->defaultValue(0),
        ]);
        $this->addForeignKey('mkexam_conclution_subject_id', 'exam_conclution', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mkexam_conclution_subject_id', 'exam_conclution');
        $this->dropTable('{{%exam_conclution}}');
    }
}
