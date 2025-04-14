<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_answer_sub_question}}`.
 */
class m220325_134402_create_exam_student_answer_sub_question_table extends Migration
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
        $this->createTable('{{%exam_student_answer_sub_question}}', [
            'id' => $this->primaryKey(),

            'file' => $this->string(255)->Null(),
            'exam_student_answer_id' => $this->integer()->notNull(),
            'sub_question_id' => $this->integer()->notNull(),
            'answer' => $this->text()->Null(),
            'ball' => $this->double()->Null(),
            'max_ball' => $this->double()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'student_created_at' => $this->integer()->Null()->comment('student yaratgan payt'),
            'student_updated_at' => $this->integer()->Null()->comment('student ozgartirgan payt'),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('esasqesa_exam_student_answer_sub_question_exam_student_answer', 'exam_student_answer_sub_question', 'exam_student_answer_id', 'exam_student_answer', 'id');
        $this->addForeignKey('esasqsq_exam_student_answer_sub_question_sub_question', 'exam_student_answer_sub_question', 'sub_question_id', 'sub_question', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('esasqesa_exam_student_answer_sub_question_exam_student_answer', 'exam_student_answer_sub_question');
        $this->dropForeignKey('esasqsq_exam_student_answer_sub_question_sub_question', 'exam_student_answer_sub_question');

        $this->dropTable('{{%exam_student_answer_sub_question}}');
    }
}
