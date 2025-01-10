<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend}}`.
 */
class m230716_055449_create_attend_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'attend';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend');
        }

        $this->createTable('{{%attend}}', [
            'id' => $this->primaryKey(),
            'time_table_id' => $this->integer()->notNull(),
            'date' => $this->date()->notNull(),
            'student_ids' => $this->json()->null(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),
            'type' => $this->tinyInteger(1)->defaultValue(1)->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_attend_table_time_table_table', 'attend', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('mk_attend_table_subject_table', 'attend', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_attend_table_subject_category_table', 'attend', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_attend_table_edu_year_table', 'attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_attend_table_edu_semestr_table', 'attend', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_attend_table_faculty_table', 'attend', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_attend_table_edu_plan_table', 'attend', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_attend_table_semestr_table', 'attend', 'semestr_id', 'semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_attend_table_time_table_table', 'attend');
        $this->dropForeignKey('mk_attend_table_subject_table', 'attend');
        $this->dropForeignKey('mk_attend_table_subject_category_table', 'attend');
        $this->dropForeignKey('mk_attend_table_edu_year_table', 'attend');
        $this->dropForeignKey('mk_attend_table_edu_semestr_table', 'attend');
        $this->dropForeignKey('mk_attend_table_faculty_table', 'attend');
        $this->dropForeignKey('mk_attend_table_edu_plan_table', 'attend');
        $this->dropForeignKey('mk_attend_table_semestr_table', 'attend');
        $this->dropTable('{{%attend}}');
    }
}
