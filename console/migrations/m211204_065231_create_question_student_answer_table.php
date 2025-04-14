<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%question_student_answer}}`.
 */
class m211204_065231_create_question_student_answer_table extends Migration
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
        $this->createTable('{{%question_student_answer}}', [
            'id' => $this->primaryKey(),

            'file' => $this->string(255)->Null(),
            'exam_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'question_type_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->Null(),
            'answer' => $this->text()->Null(),
            'ball' => $this->integer()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),
            'teacher_description' => $this->text()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('qsae_question_student_answer_exam', 'question_student_answer', 'exam_id', 'exam', 'id');
        $this->addForeignKey('qsaq_question_student_answer_question', 'question_student_answer', 'question_id', 'question', 'id');
        $this->addForeignKey('qsaqt_question_student_answer_student', 'question_student_answer', 'question_type_id', 'question_type', 'id');
        $this->addForeignKey('qsas_question_student_answer_student', 'question_student_answer', 'student_id', 'student', 'id');
        $this->addForeignKey('qsao_question_student_answer_option', 'question_student_answer', 'option_id', 'question_option', 'id');
        $this->addForeignKey('qsata_question_student_answer_teacher_access', 'question_student_answer', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('qsae_question_student_answer_exam', 'question_student_answer');
        $this->dropForeignKey('qsaq_question_student_answer_question', 'question_student_answer');
        $this->dropForeignKey('qsaqt_question_student_answer_student', 'question_student_answer');
        $this->dropForeignKey('qsas_question_student_answer_student', 'question_student_answer');
        $this->dropForeignKey('qsao_question_student_answer_option', 'question_student_answer');
        $this->dropForeignKey('qsata_question_student_answer_teacher_access', 'question_student_answer');

        $this->dropTable('{{%question_student_answer}}');
    }
}
