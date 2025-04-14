<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_teacher_check}}`.
 */
class m211120_113736_create_exam_teacher_check_table extends Migration
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
        $this->createTable('{{%exam_teacher_check}}', [
            'id' => $this->primaryKey(),

            'teacher_access_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'exam_id' => $this->integer()->notNull(),

            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('etchs_exam_teacher_check_teacher_access_relection_mk', 'exam_teacher_check', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('etchs_exam_teacher_check_student_relection_mk', 'exam_teacher_check', 'student_id', 'student', 'id');
        $this->addForeignKey('etchs_exam_teacher_check_exam_relection_mk', 'exam_teacher_check', 'exam_id', 'exam', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('etchs_exam_teacher_check_student_relection_mk', 'exam_teacher_check');
        $this->dropForeignKey('etchs_exam_teacher_check_exam_relection_mk', 'exam_teacher_check');
        $this->dropForeignKey('etchs_exam_teacher_check_teacher_access_relection_mk', 'exam_teacher_check');

        $this->dropTable('{{%exam_teacher_check}}');
    }
}
