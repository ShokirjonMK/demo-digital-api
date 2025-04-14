<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%intensive_app}}`.
 */
class m240403_114202_create_intensive_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'intensive_app';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('intensive_app');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable(
            '{{%intensive_app}}',
            [
                'id' => $this->primaryKey(),
                'student_id' => $this->integer()->notNull(),
                'subject_id' => $this->integer()->null(),
                'amount' => $this->double()->null(),
                'payed_amount' => $this->double()->null(),
                'payment_status' => $this->tinyInteger(1)->defaultValue(0)->comment('0-payment is expected. 1-payment approved. 2-payment did not reach or another error was observed '),

                'edu_semestr_subject_id' => $this->integer()->notNull(),
                'edu_semestr_id' => $this->integer()->null(),
                'faculty_id' => $this->integer()->null(),
                'edu_plan_id' => $this->integer()->null(),

                'course_id' => $this->integer()->null(),
                'semestr_id' => $this->integer()->null(),

                'file' => $this->string()->null(),


                'order' => $this->tinyInteger(1)->defaultValue(1),
                'status' => $this->tinyInteger(1)->defaultValue(1),
                'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
                'created_at' => $this->integer()->notNull()->defaultValue(0),
                'updated_at' => $this->integer()->notNull()->defaultValue(0),
                'created_by' => $this->integer()->notNull()->defaultValue(0),
                'updated_by' => $this->integer()->notNull()->defaultValue(0),
                'archived' => $this->integer()->notNull()->defaultValue(0),
            ],
            $tableOptions
        );

        $this->addForeignKey('intensiv_app_student_id', 'intensive_app', 'student_id', 'student', 'id');
        $this->addForeignKey('intensiv_app_subject_id', 'intensive_app', 'subject_id', 'subject', 'id');
        $this->addForeignKey('intensiv_app_edu_semestr_subject_id', 'intensive_app', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('intensiv_app_edu_semestr_id', 'intensive_app', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('intensiv_app_faculty_id', 'intensive_app', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('intensiv_app_edu_plan_id', 'intensive_app', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('intensiv_app_student_id', 'intensive_app');
        $this->dropForeignKey('intensiv_app_subject_id', 'intensive_app');
        $this->dropForeignKey('intensiv_app_edu_semestr_subject_id', 'intensive_app');
        $this->dropForeignKey('intensiv_app_edu_semestr_id', 'intensive_app');
        $this->dropForeignKey('intensiv_app_faculty_id', 'intensive_app');
        $this->dropForeignKey('intensiv_app_edu_plan_id', 'intensive_app');

        $this->dropTable('{{%intensive_app}}');
    }
}
