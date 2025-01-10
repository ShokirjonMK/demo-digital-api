<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student}}`.
 */
class m230511_093543_create_student_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student');
        }

        $this->createTable('{{%student}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->null(),
            'tutor_id' => $this->integer()->Null(),
            'faculty_id' => $this->integer()->Null(),
            'direction_id' => $this->integer()->Null(),
            'course_id' => $this->integer()->Null(),
            'edu_year_id' => $this->integer()->Null(),
            'edu_type_id' => $this->integer()->Null(),
            'edu_form_id' => $this->integer()->Null(),
            'edu_lang_id' => $this->integer()->Null(),
            'edu_plan_id' => $this->integer()->Null(),
            'is_contract' => $this->integer()->Null(),
            'diplom_number' => $this->integer()->null(),
            'diplom_seria' => $this->string(255)->Null(),
            'diplom_date' => $this->date()->Null(),
            'description' => $this->text()->Null(),
            'gender' => $this->tinyInteger(1)->Null(),
            'social_category_id' => $this->integer()->null(),
            'residence_status_id' => $this->integer()->null(),
            'category_of_cohabitant_id' => $this->integer()->null(),
            'student_category_id' => $this->integer()->null(),
            'partners_count' => $this->integer()->Null(),
            'live_location' => $this->text()->Null(),
            'parent_phone' => $this->string(55)->Null(),
            'res_person_phone' => $this->string(55)->Null(),
            'last_education' => $this->text()->Null(),

            'type' => $this->tinyInteger(1)->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('mk_student_table_users_table', 'student', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_student_table_group_table', 'student', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_student_table_tutor_table', 'student', 'tutor_id', 'users', 'id');
        $this->addForeignKey('mk_student_table_faculty_table', 'student', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_student_table_direction_table', 'student', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_student_table_course_table', 'student', 'course_id', 'course', 'id');
        $this->addForeignKey('mk_student_table_edu_year_table', 'student', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_student_table_edu_type_table', 'student', 'edu_type_id', 'edu_type', 'id');
        $this->addForeignKey('mk_student_table_edu_form_table', 'student', 'edu_form_id', 'edu_form', 'id');
//        $this->addForeignKey('mk_student_table_languages_table', 'student', 'edu_lang_id', 'languages', 'id');
        $this->addForeignKey('mk_student_table_edu_plan_table', 'student', 'edu_plan_id', 'edu_plan', 'id');


        $this->addForeignKey('mk_student_table_social_category_table', 'student', 'social_category_id', 'social_category', 'id');
        $this->addForeignKey('mk_student_table_residence_status_table', 'student', 'residence_status_id', 'residence_status', 'id');
        $this->addForeignKey('mk_student_table_category_of_cohabitant_table', 'student', 'category_of_cohabitant_id', 'category_of_cohabitant', 'id');
        $this->addForeignKey('mk_student_table_student_category_table', 'student', 'student_category_id', 'student_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_student_table_users_table', 'student');
        $this->dropForeignKey('mk_student_table_group_table', 'student');
        $this->dropForeignKey('mk_student_table_tutor_table', 'student');
        $this->dropForeignKey('mk_student_table_faculty_table', 'student');
        $this->dropForeignKey('mk_student_table_direction_table', 'student');
        $this->dropForeignKey('mk_student_table_course_table', 'student');
        $this->dropForeignKey('mk_student_table_edu_year_table', 'student');
        $this->dropForeignKey('mk_student_table_edu_type_table', 'student');
        $this->dropForeignKey('mk_student_table_edu_form_table', 'student');
//        $this->dropForeignKey('mk_student_table_languages_table', 'student');
        $this->dropForeignKey('mk_student_table_edu_plan_table', 'student');

        $this->dropForeignKey('mk_student_table_social_category_table', 'student');
        $this->dropForeignKey('mk_student_table_residence_status_table', 'student');
        $this->dropForeignKey('mk_student_table_category_of_cohabitant_table', 'student');
        $this->dropForeignKey('mk_student_table_student_category_table', 'student');

        $this->dropTable('{{%student}}');
    }
}
