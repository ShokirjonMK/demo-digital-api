<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_club}}`.
 */
class m220930_094340_create_student_club_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_club';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_club');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_club}}', [
            'id' => $this->primaryKey(),

            'club_category_id' => $this->integer()->null(),
            'club_time_id' => $this->integer()->notNull(),
            'club_id' => $this->integer()->null(),
            'student_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'gender' => $this->tinyInteger(1)->defaultValue(1),

            'is_leader' => $this->tinyInteger(1)->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('ctcc_student_club_club_category_id', 'student_club', 'club_category_id', 'club_category', 'id');
        $this->addForeignKey('ctct_student_club_club_time_id', 'student_club', 'club_time_id', 'club_time', 'id');
        $this->addForeignKey('ctct_student_club_club_id', 'student_club', 'club_id', 'club', 'id');
        $this->addForeignKey('cts_student_club_student_id', 'student_club', 'student_id', 'student', 'id');
        $this->addForeignKey('ctf_student_club_faculty_id', 'student_club', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('ctep_student_club_edu_plan_id', 'student_club', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('ctcc_student_club_club_category_id', 'student_club');
        $this->dropForeignKey('ctct_student_club_club_time_id', 'student_club');
        $this->dropForeignKey('ctct_student_club_club_id', 'student_club');
        $this->dropForeignKey('cts_student_club_student_id', 'student_club');
        $this->dropForeignKey('ctf_student_club_faculty_id', 'student_club');
        $this->dropForeignKey('ctep_student_club_edu_plan_id', 'student_club');
        $this->dropTable('{{%student_club}}');
    }
}
