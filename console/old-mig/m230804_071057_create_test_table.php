<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%test}}`.
 */
class m230804_071057_create_test_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'test';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('test');
        }


        $this->createTable('{{%test}}', [
            'id' => $this->primaryKey(),

            'subject_id' => $this->integer()->null(),
            'subject_semestr_id' => $this->integer()->null(),
            'kafedra_id' => $this->integer()->null(),
            'exam_type_id' => $this->integer()->null(),
            'subject_topic_id' => $this->integer()->null(),
            'language_id' => $this->integer()->null(),

            'is_checked' => $this->integer()->defaultValue(0),
            'level' => $this->integer()->defaultValue(0),
            'ball' => $this->integer()->defaultValue(1),
            'type' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_test_table_subject_table', 'test', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_test_table_subject_semestr_table', 'test', 'subject_semestr_id', 'subject_semestr', 'id');
        $this->addForeignKey('mk_test_table_kafedra_table', 'test', 'kafedra_id', 'kafedra', 'id');
        $this->addForeignKey('mk_test_table_exam_type_table', 'test', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('mk_test_table_subject_topic_table', 'test', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('mk_test_table_language_table', 'test', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_test_table_subject_topic_table', 'test');
        $this->dropTable('{{%test}}');
    }
}
