<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_staff_log}}`.
 */
class m240905_044738_create_kpi_staff_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'tech_issue';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('tech_issue');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%kpi_staff_log}}', [
            'id' => $this->primaryKey(),
            'kpi_staff_id' => $this->integer()->notNull(),
            'user_access_id' => $this->integer()->null(),

            'user_id' => $this->integer()->null(),
            'job_title_id' => $this->integer()->null(),
            'user_access_type_id' => $this->integer()->null(),
            'table_id' => $this->integer()->null(),
            'work_rate_id' => $this->integer()->null(),
            'work_type' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null()->defaultValue(1),
            'in_doc_all' => $this->integer(),
            'in_doc_on_time' => $this->integer(),
            'in_doc_after_time' => $this->integer(),
            'in_doc_not_done' => $this->integer(),
            'in_doc_ball' => $this->double(),
            'in_doc_percent' => $this->double(),
            'in_doc_collected_ball' => $this->double(),
            'ex_doc_all' => $this->integer(),
            'ex_doc_on_time' => $this->integer(),
            'ex_doc_after_time' => $this->integer(),
            'ex_doc_not_done' => $this->integer(),
            'ex_doc_ball' => $this->double(),
            'ex_doc_percent' => $this->double(),
            'ex_doc_collected_ball' => $this->double(),
            'ball_dep_lead' => $this->double(),
            'file_dep_lead' => $this->string(),
            'plan_file' => $this->string(),
            'work_file' => $this->string(),
            'ball_rector' => $this->double(),
            'ball_commission' => $this->double(),
            'ball_all' => $this->double(),
            'kpi' => $this->double(),


            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),

            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%kpi_staff_log}}');
    }
}
