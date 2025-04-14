<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_answer}}`.
 */
class m211204_065232_create_student_answer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_answer';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_answer');
        }
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%exam_student_answer}}', [
            'id' => $this->primaryKey(),
            'exam_student_id' => $this->integer()->null(),
            'file' => $this->string(255)->Null(),
            'exam_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'option_id' => $this->integer()->Null(),
            'answer' => $this->text()->Null(),
            'ball' => $this->double()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->defaultValue(1),
            'type' => $this->tinyInteger(1)->notNull(),

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

        $this->addForeignKey('ses_exam_student_answer_exam', 'exam_student_answer', 'exam_id', 'exam', 'id');
        $this->addForeignKey('ses_exam_student_answer_exam_student_id', 'exam_student_answer', 'exam_student_id', 'exam_student', 'id');
        $this->addForeignKey('ses_exam_student_answer_exam_question', 'exam_student_answer', 'question_id', 'question', 'id');
        $this->addForeignKey('ses_exam_student_answer_student', 'exam_student_answer', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_ses_exam_student_answer_option', 'exam_student_answer', 'option_id', 'question_option', 'id');
        $this->addForeignKey('ses_exam_student_answer_teacher_access', 'exam_student_answer', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ses_exam_student_answer_exam', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_exam_student_id', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_exam_question', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_student', 'exam_student_answer');
        $this->dropForeignKey('mk_ses_exam_student_answer_option', 'exam_student_answer');
        $this->dropForeignKey('ses_exam_student_answer_teacher_access', 'exam_student_answer');


        $this->dropTable('{{%exam_student_answer}}');
    }
}
