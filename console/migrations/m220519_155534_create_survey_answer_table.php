<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%survey_answer}}`.
 */
class m220519_155534_create_survey_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'survey_answer';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('survey_answer');
        }

        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%survey_answer}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'survey_question_id' => $this->integer()->notNull(),
            'ball' => $this->double()->notNull(),
            'exam_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'student_id' => $this->integer()->null(),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('sasq_survey_answer_survey_question', 'survey_answer', 'survey_question_id', 'survey_question', 'id');
        $this->addForeignKey('sas_survey_answer_subject', 'survey_answer', 'subject_id', 'subject', 'id');
        $this->addForeignKey('sae_survey_answer_exam', 'survey_answer', 'exam_id', 'exam', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sasq_survey_answer_survey_question', 'survey_answer');
        $this->dropForeignKey('sas_survey_answer_subject', 'survey_answer');
        $this->dropForeignKey('sae_survey_answer_exam', 'survey_answer');

        $this->dropTable('{{%survey_answer}}');
    }
}
