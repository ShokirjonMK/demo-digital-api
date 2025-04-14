<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_gpa_old}}`.
 */
class m230425_055415_create_student_gpa_old_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_gpa_old';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_gpa_old');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_gpa_old}}', [
            'id' => $this->primaryKey(),
            'last_name' => $this->string(255),
            'first_name' => $this->string(255),
            'middle_name' => $this->string(255),
            'direction' => $this->string(255),
            'course' => $this->string(255),
            'group' => $this->string(255),
            'semestr' => $this->string(255),
            'edu_lang' => $this->string(255),
            'subject_name' => $this->string(255),
            'username_distant' => $this->string(255),
            'srs_id' => $this->string(255),
            'all_ball' => $this->double(),
            'alphabet' => $this->string(255),
            'mark' => $this->string(255),
            'student_id' => $this->integer()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_student_gpa_old_student_id', 'student_gpa_old', 'student_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_gpa_old_student_id', 'student_gpa_old');
        $this->dropTable('{{%student_gpa_old}}');
    }
}
