<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_question}}`.
 */
class m211115_154019_create_exam_question_table extends Migration
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
        $this->createTable('{{%exam_question}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            // name in translate
            'file' => $this->string(255)->notNull(),
            'ball' => $this->double()->defaultValue(1),
            'question' => $this->text()->notNull(),
            'lang_id' => $this->integer()->notNull(),
            'level' => $this->tinyInteger(1)->notNull()->comment("Qiyinlilik darajasi 1-oson, 2-o\'rta, 3-murakkab"),
            'exam_question_type_id' => $this->integer()->notNull()->comment("1-savol, 2-test, 3-another"),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('eqe_exam_question_exam', 'exam_question', 'exam_id', 'exam', 'id');
        $this->addForeignKey('eql_exam_question_lang', 'exam_question', 'lang_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eqe_exam_question_exam', 'exam_question');
        $this->dropForeignKey('eql_exam_question_lang', 'exam_question');

        $this->dropTable('{{%exam_question}}');
    }
}
