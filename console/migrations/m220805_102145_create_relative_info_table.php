<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%parent_info}}`.
 */
class m220805_102145_create_relative_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'relative_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('relative_info');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%relative_info}}', [
            'id' => $this->primaryKey(),

            'r_last_name' => $this->string(255)->null(),
            'r_first_name' => $this->string(255)->null(),
            'r_middle_name' => $this->string(255)->null(),
            'r_type' => $this->tinyInteger(1)->defaultValue(1)->comment("1-otasi 2- onasi, ...."),
            'r_birthday' => $this->date()->null(),
            'r_birth_address' => $this->string(255)->null(),
            'r_address' => $this->string(255)->null(),
            'r_work_place' => $this->string(255)->null(),
            'r_work_position' => $this->string(255)->null(),
            'r_phone' => $this->string(55)->null(),
            'user_type' => $this->integer()->defaultValue(1)->comment("1-student, 2-teacher, 3-xodim"),
            'user_id' => $this->integer(11)->notNull(),
            'r_description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);
        // User
        $this->addForeignKey('relative_info_user_id', 'relative_info', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // user
        $this->dropForeignKey('relative_info_user_id', 'relative_info');



        $this->dropTable('{{%relative_info}}');
    }
}
