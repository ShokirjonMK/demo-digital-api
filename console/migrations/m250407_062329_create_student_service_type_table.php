<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student_service_type}}`.
 */
class m250407_062329_create_student_service_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_service_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_service_type');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%student_service_type}}', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger(1)->defaultValue(1)->comment('1- auto 2-manual'),
            'category' => $this->tinyInteger(1)->defaultValue(1)->comment('1- murojaat, 2-kategoriya turlari uchun'),
            'type_id' => $this->integer()->null()->comment('o`zini id si category nomini olish uchun'),

            'text' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'link' => $this->string(255)->null(),


            'order' => $this->tinyInteger(1)->defaultValue(1)->comment('Order of the item'),
            'status' => $this->tinyInteger(1)->defaultValue(1)->comment('Status of the item (1 = active, 0 = inactive)'),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0)->comment('Is the item deleted (0 = no, 1 = yes)'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('Creation timestamp'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('Update timestamp'),
            'created_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who created the record'),
            'updated_by' => $this->integer()->notNull()->defaultValue(0)->comment('ID of the user who last updated the record'),
            'archived' => $this->integer()->notNull()->defaultValue(0)->comment('Is the item archived (0 = no, 1 = yes)'),
        ], $tableOptions);

        $this->addForeignKey('fk-student_service_type-type_id', '{{%student_service_type}}', 'type_id', '{{%student_service_type}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student_service_type}}');
    }
}
