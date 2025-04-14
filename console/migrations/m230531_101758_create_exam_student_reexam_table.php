<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_reexam}}`.
 */
class m230531_101758_create_exam_student_reexam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_reexam';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_reexam');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%exam_student_reexam}}', [
            'id' => $this->primaryKey(),

            'file' => $this->string(255)->null(),
            'description' => $this->text()->null(),
            'student_id' => $this->integer(11)->null(),
            'exam_student_id' => $this->integer(11)->null(),
            'subject_id' => $this->integer(11)->null(),
            'exam_id' => $this->integer(11)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->null()->defaultValue(0),
            'updated_by' => $this->integer()->null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->null()->defaultValue(0),
        ]);
        $this->addForeignKey('exam_student_reexam_student_id', 'exam_student_reexam', 'student_id', 'student', 'id');
        $this->addForeignKey('exam_student_reexam_exam_student_id', 'exam_student_reexam', 'exam_student_id', 'exam_student', 'id');
        $this->addForeignKey('exam_student_reexam_subject_id', 'exam_student_reexam', 'subject_id', 'subject', 'id');
        $this->addForeignKey('exam_student_reexam_exam_id', 'exam_student_reexam', 'exam_id', 'exam', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('exam_student_reexam_student_id', 'exam_student_reexam');
        $this->dropForeignKey('exam_student_reexam_exam_student_id', 'exam_student_reexam');
        $this->dropForeignKey('exam_student_reexam_subject_id', 'exam_student_reexam');
        $this->dropForeignKey('exam_student_reexam_exam_id', 'exam_student_reexam');
        $this->dropTable('{{%exam_student_reexam}}');
    }
}
