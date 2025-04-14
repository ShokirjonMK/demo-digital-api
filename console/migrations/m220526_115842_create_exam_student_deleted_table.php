<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_deleted}}`.
 */
class m220526_115842_create_exam_student_deleted_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_deleted';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_deleted');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%exam_student_deleted}}', [
            'id' => $this->primaryKey(),
            'exam_student_id' => $this->integer()->Null(),
            'student_id' => $this->integer()->Null(),
            'start' => $this->integer()->Null(),
            'finish' => $this->integer()->Null(),
            'exam_id' => $this->integer()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->Null(),
            'lang_id' => $this->integer()->Null(),
            'exam_semeta_id' => $this->integer()->Null(),
            'is_plagiat' => $this->integer()->Null(),
            'duration' => $this->integer()->Null(),
            'ball' => $this->double()->Null(),
            'plagiat_file' => $this->string()->Null(),
            'password' => $this->integer()->Null(),
            'plagiat_percent' => $this->integer()->Null(),
            'conclusion' => $this->string()->Null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->Null()->defaultValue(0),
            'updated_by' => $this->integer()->Null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->Null()->defaultValue(0),

        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student_deleted}}');
    }
}
