<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_category_info}}`.
 */
class m220607_061126_create_kpi_category_info_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'kpi_category_info';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_category_info');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%kpi_category_info}}', [
            'id' => $this->primaryKey(),

            'kpi_category_id' => $this->integer()->notNull(),
            'lang' => $this->string(3)->null(),
            'name' => $this->text()->null(),
            'description' => $this->text()->null(),
            'tab_name' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('kpicikc_kpi_category_info_kpi_category', 'kpi_category_info', 'kpi_category_id', 'kpi_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('kpicikc_kpi_category_info_kpi_category', 'kpi_category_info');
        $this->dropTable('{{%kpi_category_info}}');
    }
}
