<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_semestr_exams_type}}`.
 */
class m230528_071715_create_edu_semestr_exams_type_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'edu_semestr_exams_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('edu_semestr_exams_type');
        }

        $this->createTable('{{%edu_semestr_exams_type}}', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'exams_type_id' => $this->integer()->notNull(),
            'max_ball' => $this->integer()->notNull(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_edu_semestr_exams_type_table_edu_semestr_subject_table', 'edu_semestr_exams_type', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_edu_semestr_exams_type_table_exams_type_table', 'edu_semestr_exams_type', 'exams_type_id', 'exams_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_semestr_exams_type_table_edu_semestr_subject_table', 'edu_semestr_exams_type');
        $this->dropForeignKey('mk_edu_semestr_exams_type_table_exams_type_table', 'edu_semestr_exams_type');
        $this->dropTable('{{%edu_semestr_exams_type}}');
    }
}
