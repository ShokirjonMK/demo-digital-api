<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_order}}`.
 */
class m221129_102216_create_student_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_order';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_order');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%student_order}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer(11)->notNull(),
            'order_type_id' => $this->integer()->notNull(),
            'user_id' => $this->integer(11)->null(),
            'date' => $this->date()->null(),
            'file' => $this->string(255)->null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        //        // Student
        //        $this->addForeignKey('sos_student_order_student_id', 'student_order', 'student_id', 'student', 'id');
        // User
        $this->addForeignKey('sto_student_order_student_id', 'student_order', 'student_id', 'student', 'id');
        $this->addForeignKey('sto_student_order_order_type_id', 'student_order', 'order_type_id', 'order_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sto_student_order_student_id', 'student_order');
        $this->dropForeignKey('sto_student_order_order_type_id', 'student_order');

        $this->dropTable('{{%student_order}}');
    }
}
