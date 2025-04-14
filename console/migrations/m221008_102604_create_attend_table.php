<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend}}`.
 */
class m221008_102604_create_attend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'attend';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%attend}}', [
            'id' => $this->primaryKey(),

            'date' => $this->date()->notNull(),
            'student_ids' => $this->json()->null(),
            'time_table_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'time_option_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'semestr_id' => $this->integer()->null(),

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
        $this->addForeignKey('amk_attend_time_table', 'attend', 'time_table_id', 'time_table', 'id');
        $this->addForeignKey('amk_attend_subject', 'attend', 'subject_id', 'subject', 'id');
        $this->addForeignKey('amk_attend_subject_category', 'attend', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('amk_attend_time_option', 'attend', 'time_option_id', 'time_option', 'id');
        $this->addForeignKey('amk_attend_edu_year', 'attend', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('amk_attend_edu_semestr', 'attend', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('amk_attend_faculty_id', 'attend', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('amk_attend_edu_plan_id', 'attend', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('amk_attend_time_table', 'attend');
        $this->dropForeignKey('amk_attend_subject', 'attend');
        $this->dropForeignKey('amk_attend_subject_category', 'attend');
        $this->dropForeignKey('amk_attend_time_option', 'attend');
        $this->dropForeignKey('amk_attend_edu_year', 'attend');
        $this->dropForeignKey('amk_attend_edu_semestr', 'attend');
        $this->dropForeignKey('amk_attend_faculty_id', 'attend');
        $this->dropForeignKey('amk_attend_edu_plan_id', 'attend');
        $this->dropTable('{{%attend}}');
    }
}
