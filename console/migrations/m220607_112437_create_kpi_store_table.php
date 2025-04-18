<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_store}}`.
 */
class m220607_112437_create_kpi_store_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'kpi_store';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_store');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%kpi_store}}', [
            'id' => $this->primaryKey(),

            'kpi_category_id' => $this->integer()->notNull(),
            'date' => $this->date()->Null(),
            'file' => $this->string(255)->null(),
            'subject_category_id' => $this->integer()->Null(),
            'count_of_copyright' => $this->integer()->defaultValue(0),
            'link' => $this->string(255)->null(),
            'ball' => $this->double()->defaultValue(0),
            'user_id' => $this->integer()->notNull(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('kpiskc_kpi_store_kpi_category', 'kpi_store', 'kpi_category_id', 'kpi_category', 'id');
        $this->addForeignKey('kpissc_kpi_store_subject_category', 'kpi_store', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('kpissc_kpi_store_user', 'kpi_store', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('kpiskc_kpi_store_kpi_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_store_subject_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_store_user', 'kpi_store');

        $this->dropTable('{{%kpi_store}}');
    }
}
