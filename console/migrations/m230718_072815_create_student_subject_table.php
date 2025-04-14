<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_subject}}`.
 */
class m230718_072815_create_student_subject_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_subject');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student_subject}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),
            
            'control_ball' => $this->integer(11)->null()->comment("oraliq ballari umumiysi semestr bo'yicha"),
            'exam_ball' => $this->integer(11)->null()->comment("yakuniy bali umumiysi semestr bo'yicha"),

            'course_id' => $this->integer(11)->null(),
            'semestr_id' => $this->integer(11)->null(),
            'edu_semestr_id' => $this->integer(11)->null(),
            'edu_semestr_subject_id' => $this->integer(11)->null(),
            'attempt' => $this->tinyInteger(1)->defaultValue(1)->comment("nechinchi marta topshirayotgani"),

            'edu_year_id' => $this->integer(11)->null(),
            'faculty_id' => $this->integer(11)->null(),
            'direction_id' => $this->integer(11)->null(),
            'edu_plan_id' => $this->integer(11)->null(),

            'data' => $this->json()->Null()->comment('yozilgan ballarni qayerdan kelganini yozib ketish uchun'),

            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->addForeignKey('mark_student_subject_student_id', 'student_subject', 'student_id', 'student', 'id');
        $this->addForeignKey('mark_student_subject_subject_id', 'student_subject', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mark_student_subject_edu_year_id', 'student_subject', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mark_student_subject_student_id', 'student_subject');
        $this->dropForeignKey('mark_student_subject_subject_id', 'student_subject');
        $this->dropForeignKey('mark_student_subject_edu_year_id', 'student_subject');

        $this->dropTable('{{%student_subject}}');
    }
}
