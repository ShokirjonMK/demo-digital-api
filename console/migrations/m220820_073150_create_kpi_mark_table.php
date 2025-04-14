<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_mark}}`.
 */
class m220820_073150_create_kpi_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'kpi_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_mark');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%kpi_mark}}', [
            'id' => $this->primaryKey(),

            'kpi_category_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'ball' => $this->double()->Null(),
            'edu_year_id' => $this->integer(11)->null(),
            'type' => $this->tinyInteger(1)->defaultValue(1),


            'status' => $this->tinyInteger(1)->defaultValue(1),

            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('kpi_mark_kpi_user_id', 'kpi_mark', 'user_id', 'users', 'id');
        $this->addForeignKey('kpi_mark_kpi_category_id', 'kpi_mark', 'kpi_category_id', 'kpi_category', 'id');
        $this->addForeignKey('kpi_mark_kpi_edu_year_id', 'kpi_mark', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('kpi_mark_user_id', 'kpi_mark');
        $this->dropForeignKey('kpi_mark_kpi_category_id', 'kpi_mark');
        $this->dropForeignKey('kpi_mark_kpi_edu_year_id', 'kpi_mark');


        $this->dropTable('{{%kpi_mark}}');
    }
}
