<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%other_certificate}}`.
 */
class m220805_102020_create_other_certificate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'other_certificate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('other_certificate');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%other_certificate}}', [
            'id' => $this->primaryKey(),

            'other_certificate_type_id' => $this->integer(11)->null(),
            'address' => $this->string(255)->null(),
            'year' => $this->string(11)->null(),
            'file' => $this->string(255),
            'student_id' => $this->integer(11)->Null(),
            'user_id' => $this->integer(11)->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        //        // Student
        //        $this->addForeignKey('other_certificate-student_id', 'sport_certificate', 'student_id', 'student', 'id');
        //        // User
        $this->addForeignKey('ocu_other_certificate_user_id', 'other_certificate', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //        // student
        //        $this->dropForeignKey('other_certificate-student_id', 'sport_certificate');
        //
        //        // user
        $this->dropForeignKey('ocu_other_certificate_user_id', 'other_certificate');
        //

        $this->dropTable('{{%other_certificate}}');
    }
}
