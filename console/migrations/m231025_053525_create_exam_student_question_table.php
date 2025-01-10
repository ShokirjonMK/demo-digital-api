<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_question}}`.
 */
class m231025_053525_create_exam_student_question_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam_student_question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_question');
        }
        $this->createTable('{{%exam_student_question}}', [
            'id' => $this->primaryKey(),
            'exam_student_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->null(),
            'exam_id' => $this->integer()->null(),
            'group_id' => $this->integer()->null(),
            'type' => $this->integer()->null(),
            'student_ball' => $this->double()->defaultValue(0),
            'exam_test_id' => $this->integer()->notNull(),

            'answer_text' => $this->text()->null(),
            'file' => $this->string(255)->null(),

            'student_option' => $this->integer()->defaultValue(0),
            'options' => $this->string(255)->null(),

            'is_correct' => $this->tinyInteger(1)->defaultValue(0),

            'description' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_exam_student_question_table_exam_student_table', 'exam_student_question', 'exam_student_id', 'exam_student', 'id');
        $this->addForeignKey('mk_exam_student_question_table_student_table', 'exam_student_question', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_exam_student_question_table_student_user_table', 'exam_student_question', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_student_question_table_exam_table', 'exam_student_question', 'exam_id', 'exam', 'id');
        $this->addForeignKey('mk_exam_student_question_table_group_table', 'exam_student_question', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_exam_student_question_table_exam_test_table', 'exam_student_question', 'exam_test_id', 'test', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student_question}}');
    }
}
