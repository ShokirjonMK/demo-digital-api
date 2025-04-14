<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_log}}`.
 */
class m231216_083418_create_exam_log_table extends Migration
{

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_log';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_log');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }


        $this->createTable('{{%exam_log}}', [
            'id' => $this->primaryKey(),
            'archived' => $this->integer()->defaultValue(0),
            'old_exam_id' => $this->integer(),
            'exam_id' => $this->integer(),
            'question_count_by_type_with_ball' => $this->json(),
            'question_count_by_type' => $this->string(255),
            'exam_type_id' => $this->integer()->null(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'start' => $this->dateTime()->null(),
            'finish' => $this->dateTime()->null(),
            'max_ball' => $this->double()->defaultValue(0),
            'min_ball' => $this->double()->defaultValue(0),
            'order' => $this->tinyInteger()->defaultValue(1),
            'status' => $this->tinyInteger()->defaultValue(1),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer()->defaultValue(0),
            'updated_by' => $this->integer()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->defaultValue(0),
            'duration' => $this->integer(),
            'is_protected' => $this->integer()->defaultValue(0),
            'faculty_id' => $this->integer(),
            'direction_id' => $this->integer(),
            'type' => $this->integer()->defaultValue(1),
            'status_appeal' => $this->integer()->defaultValue(0),
            'appeal_start' => $this->integer(),
            'appeal_finish' => $this->integer(),
            'edu_year_id' => $this->integer(),
            'category' => $this->integer()->defaultValue(1),
            'subject_id' => $this->integer(),
            'password' => $this->string(255),
            'edu_plan_id' => $this->integer(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_log}}');
    }
}
