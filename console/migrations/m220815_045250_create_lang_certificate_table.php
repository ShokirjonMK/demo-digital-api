<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lang_certificate}}`.
 */
class m220815_045250_create_lang_certificate_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'lang_certificate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('lang_certificate');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%lang_certificate}}', [
            'id' => $this->primaryKey(),

            'certificate_type_id' => $this->integer(11)->notNull(),
            'ball' => $this->double(11)->Null(),
            'file' => $this->string(255),
            'lang' => $this->string(2)->Null(),
            'user_type' => $this->integer()->defaultValue(1)->comment('1-student, 2-teacher, 3-xodim'),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        // // Student
        // $this->addForeignKey('lang_certificate_student_id', 'lang_certificate', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('lang_certificate_user_id', 'lang_certificate', 'user_id', 'users', 'id');

        // Certificate_type
        $this->addForeignKey('lang_certificate_certificate_type_id', 'lang_certificate', 'certificate_type_id', 'lang_certificate_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('lang_certificate_user_id', 'lang_certificate');

        $this->dropForeignKey('lang_certificate_certificate_type_id', 'lang_certificate');

        $this->dropTable('{{%lang_certificate}}');
    }
}
