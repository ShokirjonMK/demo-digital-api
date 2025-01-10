<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend_reason}}`.
 */
class m230716_070318_create_attend_reason_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'attend_reason';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_reason');
        }

        $this->createTable('{{%attend_reason}}', [
            'id' => $this->primaryKey(),
            'is_confirmed' => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'start' => $this->dateTime()->notNull(),
            'end' => $this->dateTime()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'file' => $this->string(255)->null(),
            'description' => $this->text()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_attend_reason_table_student_table', 'attend_reason', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_attend_reason_table_subject_table', 'attend_reason', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_attend_reason_table_faculty_table', 'attend_reason', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_attend_reason_table_edu_plan_table', 'attend_reason', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_attend_reason_table_edu_year_table', 'attend_reason', 'edu_year_id', 'edu_year', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_attend_reason_table_student_table', 'attend_reason');
        $this->dropForeignKey('mk_attend_reason_table_subject_table', 'attend_reason');
        $this->dropForeignKey('mk_attend_reason_table_faculty_table', 'attend_reason');
        $this->dropForeignKey('mk_attend_reason_table_edu_plan_table', 'attend_reason');
        $this->dropTable('{{%attend_reason}}');
    }
}
