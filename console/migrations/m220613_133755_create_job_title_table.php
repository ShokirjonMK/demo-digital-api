<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job_title}}`.
 */
class m220613_133755_create_job_title_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'job_title';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('job_title');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%job_title}}', [
            'id' => $this->primaryKey(),

            'user_access_type_id' => $this->integer()->Null(),
            'table_id' => $this->integer()->Null(),
            'is_leader' => $this->tinyInteger(1)->defaultValue(0),
            'type' => $this->tinyInteger(1)->defaultValue(1),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->addForeignKey('jtuat_job_title_user_access_type_id', 'job_title', 'user_access_type_id', 'user_access_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('jtuat_job_title_user_access_type_id', 'job_title');

        $this->dropTable('{{%job_title}}');
    }
}
