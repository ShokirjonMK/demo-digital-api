<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%sport_certificate}}`.
 */
class m220805_101919_create_sport_certificate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'sport_certificate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('sport_certificate');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%sport_certificate}}', [
            'id' => $this->primaryKey(),

            'type' => $this->integer(11)->Null(),
            'date' => $this->date()->notNull(),
            'year' => $this->string(11)->Null(),
            'address' => $this->string(255)->Null(),
            'file' => $this->string(255)->Null(),
            'student_id' => $this->integer(11)->Null(),
            'user_id' => $this->integer(11)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        // Student
        $this->addForeignKey('sport_certificate_student_id', 'sport_certificate', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('sport_certificate_user_id', 'sport_certificate', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // student
        $this->dropForeignKey('sport_certificate_student_id', 'sport_certificate');

        // user
        $this->dropForeignKey('sport_certificate_user_id', 'sport_certificate');


        $this->dropTable('{{%sport_certificate}}');
    }
}
