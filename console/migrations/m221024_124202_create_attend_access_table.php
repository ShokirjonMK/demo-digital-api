<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend_access}}`.
 */
class m221024_124202_create_attend_access_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'attend_access';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_access');
        }
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%attend_access}}', [
            'id' => $this->primaryKey(),

            'start_date' => $this->date()->notNull(),
            'end_date' => $this->date()->notNull(),
            'time_table_id' => $this->integer()->null(),
            'subject_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'time_option_id' => $this->integer()->null(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),

            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1 kuz 2 bohor'),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_a_a_attend_access_time_table_id', 'attend_access', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('mk_a_a_attend_access_subject_id', 'attend_access', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_a_a_attend_access_user_id', 'attend_access', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_a_a_attend_access_edu_year_id', 'attend_access', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_a_a_attend_access_subject_category_id', 'attend_access', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_a_a_attend_access_time_option_id', 'attend_access', 'time_option_id', 'time_option', 'id');
        $this->addForeignKey('mk_a_a_attend_access_faculty_id', 'attend_access', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_a_a_attend_access_edu_plan_id', 'attend_access', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_a_a_attend_access_edu_semestr_id', 'attend_access', 'edu_semestr_id', 'edu_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_a_a_attend_access_time_table_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_subject_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_user_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_edu_year_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_subject_category_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_time_option_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_faculty_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_edu_plan_id', 'attend_access');
        $this->dropForeignKey('mk_a_a_attend_access_edu_semestr_id', 'attend_access');

        $this->dropTable('{{%attend_access}}');
    }
}
