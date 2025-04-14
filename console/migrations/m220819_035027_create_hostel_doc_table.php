<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hostel_doc}}`.
 */
class m220819_035027_create_hostel_doc_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'hostel_doc';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('hostel_doc');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%hostel_doc}}', [
            'id' => $this->primaryKey(),

            'is_checked' => $this->tinyInteger(1)->Null(),
            'student_id' => $this->integer()->notNull(),
            'hostel_app_id' => $this->integer()->notNull(),
            'hostel_category_id' => $this->integer()->notNull(),
            'hostel_category_type_id' => $this->integer()->Null(),
            'type' => $this->integer()->Null(),

            'file' => $this->string(255)->Null(),
            'start' => $this->date()->Null(),
            'finish' => $this->date()->Null(),
            'conclution' => $this->text()->Null(),
            'description' => $this->text()->Null(),
            'ball' => $this->double()->Null(),
            'data' => $this->text()->Null(),

            'user_id' => $this->integer()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'archived' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('hostel_doc_hostel_student_id', 'hostel_doc', 'student_id', 'student', 'id');
        $this->addForeignKey('hostel_doc_hostel_hostel_app_id', 'hostel_doc', 'hostel_app_id', 'hostel_app', 'id', 'CASCADE');
        $this->addForeignKey('hostel_doc_hostel_hostel_category_id', 'hostel_doc', 'hostel_category_id', 'hostel_category', 'id');
        $this->addForeignKey('hostel_doc_hostel_hostel_category_type_id', 'hostel_doc', 'hostel_category_type_id', 'hostel_category_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('hostel_doc_hostel_student_id', 'hostel_doc');
        $this->dropForeignKey('hostel_doc_hostel_hostel_app_id', 'hostel_doc');
        $this->dropForeignKey('hostel_doc_hostel_hostel_category_id', 'hostel_doc');
        $this->dropForeignKey('hostel_doc_hostel_hostel_category_type_id', 'hostel_doc');
        $this->dropTable('{{%hostel_doc}}');
    }
}
