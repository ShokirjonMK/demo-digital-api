<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_appeal}}`.
 */
class m220427_042659_create_exam_appeal_table extends Migration
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
        $this->createTable('{{%exam_appeal}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer()->null(),
            'exam_student_id' => $this->integer()->notNull(),
            'appeal_text' => $this->text()->null(),
            'teacher_user_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('eaes_exam_appeal_exam_student', 'exam_appeal', 'exam_student_id', 'exam_student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eaes_exam_appeal_exam_student', 'exam_appeal');

        $this->dropTable('{{%exam_appeal}}');
    }
}
