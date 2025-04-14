<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hostel_app}}`.
 */
class m220818_130626_create_hostel_app_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'hostel_app';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('hostel_app');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%hostel_app}}', [
            'id' => $this->primaryKey(),

            'student_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'faculty_id' => $this->integer()->Null(),
            'edu_year_id' => $this->integer()->Null(),
            'ball' => $this->double()->Null(),
            'description' => $this->text()->Null(),
            'conclution' => $this->text()->Null(),

            'building_id' => $this->integer()->Null(),
            'room_id' => $this->integer()->Null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('hostel_app_hostel_student_id', 'hostel_app', 'student_id', 'student', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('hostel_app_hostel_student_id', 'hostel_app');
        $this->dropTable('{{%hostel_app}}');
    }
}
