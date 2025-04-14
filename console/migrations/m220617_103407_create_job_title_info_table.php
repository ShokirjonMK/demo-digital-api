<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%job_title_info}}`.
 */
class m220617_103407_create_job_title_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'job_title_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('job_title_info');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%job_title_info}}', [
            'id' => $this->primaryKey(),

            'job_title_id' => $this->integer()->notNull(),
            'lang' => $this->string(2)->notNull(),
            'name' => $this->string(255)->null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('jtijt_job_title_info_job_title', 'job_title_info', 'job_title_id', 'job_title', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('jtijt_job_title_info_job_title', 'job_title_info');
        $this->dropTable('{{%job_title_info}}');
    }
}
