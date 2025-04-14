<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam}}`.
 */
class m211110_061104_create_exam_table extends Migration
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
        $this->createTable('{{%exam}}', [
            'id' => $this->primaryKey(),
            // nama translate da bo'ladi
            'exam_type_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->null(),
            'start' => $this->dateTime()->notNull(),
            'checking_at' => $this->dateTime()->notNull(),
            'checking_until' => $this->dateTime()->notNull(),
            'finish' => $this->dateTime()->notNull(),
            'password' => $this->string()->Null(),
            'max_ball' => $this->double()->defaultValue(0),
            'min_ball' => $this->double()->defaultValue(0),
            'category' => $this->integer()->Null(),
            'subject_id' => $this->integer()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('eet_exam_exam_type', 'exam', 'exam_type_id', 'exams_type', 'id');
        $this->addForeignKey('eess_exam_edu_semestr_subject', 'exam', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('eet_exam_exam_type', 'exam');
        $this->dropForeignKey('eess_exam_edu_semestr_subject', 'exam');
        $this->dropTable('{{%exam}}');
    }
}
