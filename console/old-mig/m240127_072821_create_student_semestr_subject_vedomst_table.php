<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_semestr_subject_vedomst}}`.
 */
class m240127_072821_create_student_semestr_subject_vedomst_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student_semestr_subject_vedomst';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_semestr_subject_vedomst');
        }

        $this->createTable('{{%student_semestr_subject_vedomst}}', [
            'id' => $this->primaryKey(),
            'student_semestr_subject_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->null(),
            'group_id' => $this->integer()->null(),
            'ball' => $this->integer()->defaultValue(0),
            'passed' => $this->integer()->defaultValue(0),
            'vedomst' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_s_s_s_table', 'student_semestr_subject_vedomst', 'student_semestr_subject_id', 'student_semestr_subject', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_subject_table', 'student_semestr_subject_vedomst', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_edu_year_table', 'student_semestr_subject_vedomst', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_semestr_table', 'student_semestr_subject_vedomst', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_student_table', 'student_semestr_subject_vedomst', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_student_user_table', 'student_semestr_subject_vedomst', 'student_user_id', 'users', 'id');
        $this->addForeignKey('mk_student_semestr_subject_vedomst_table_group_table', 'student_semestr_subject_vedomst', 'group_id', 'group', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_semestr_subject_vedomst}}');
    }
}
