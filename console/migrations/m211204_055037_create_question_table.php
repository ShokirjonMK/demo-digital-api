<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%question}}`.
 */
class m211204_055037_create_question_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('question');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%question}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'file' => $this->string(255)->Null(),
            'ball' => $this->double()->Null(),
            'question' => $this->text()->notNull()->comment('Savol matni yozilati'),
            'lang_id' => $this->integer()->notNull(),
            'level' => $this->tinyInteger(1)->defaultValue(1)->comment("Qiyinlilik darajasi 1-oson, 2-o\'rta, 3-murakkab"),
            'question_type_id' => $this->integer()->notNull()->comment("1-savol, 2-test, 3-another"),

            'archived' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('qc_question_course', 'question', 'course_id', 'course', 'id');
        $this->addForeignKey('qsm_question_semestr', 'question', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('qs_question_subject', 'question', 'subject_id', 'subject', 'id');
        $this->addForeignKey('qqt_question_question_type', 'question', 'question_type_id', 'question_type', 'id');
        $this->addForeignKey('ql_question_language', 'question', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('qc_question_course', 'question');
        $this->dropForeignKey('qsm_question_semestr', 'question');
        $this->dropForeignKey('qs_question_subject', 'question');
        $this->dropForeignKey('qqt_question_question_type', 'question');
        $this->dropForeignKey('ql_question_language', 'question');

        $this->dropTable('{{%question}}');
    }
}
