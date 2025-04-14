<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%kpi_data}}`.
 */
class m220806_125712_create_kpi_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableName = Yii::$app->db->tablePrefix . 'kpi_data';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('kpi_data');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%kpi_data}}', [
            'id' => $this->primaryKey(),
            'kpi_category_id' => $this->integer()->notNull(),
            'date' => $this->date()->Null(),
            'file1' => $this->string(255)->null(),
            'file2' => $this->string(255)->null(),
            'file3' => $this->string(255)->null(),
            'start_date' => $this->date()->Null(),
            'end_date' => $this->date()->Null(),
            'link' => $this->text()->null(),
            'link2' => $this->text()->null(),
            'ball' => $this->double()->null(),
            'count' => $this->integer()->Null(),
            'subject_category_id' => $this->integer()->Null(),
            'event_type' => $this->integer()->Null(),
            'event_form' => $this->integer()->Null(),
            'number' => $this->string(255)->null(),
            'level' => $this->string(255)->null(),
            'name' => $this->string(255)->null(),
            'name1' => $this->string(255)->null(),
            'name2' => $this->string(255)->null(),
            'name3' => $this->string(255)->null(),
            'authors' => $this->string(255)->null(),
            'count_of_copyright' => $this->integer()->defaultValue(0),
            'user_id' => $this->integer()->notNull(),
            'a1' => $this->string(255)->null(),
            'a2' => $this->string(255)->null(),
            'a3' => $this->string(255)->null(),
            'a4' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(0),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'is_deleted_org' => $this->integer()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('kpiskc_kpi_data_kpi_category', 'kpi_store', 'kpi_category_id', 'kpi_category', 'id');
        $this->addForeignKey('kpissc_kpi_data_subject_category', 'kpi_store', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('kpissc_kpi_data_user', 'kpi_store', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropForeignKey('kpiskc_kpi_data_kpi_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_data_subject_category', 'kpi_store');
        $this->dropForeignKey('kpissc_kpi_data_user', 'kpi_store');

        $this->dropTable('{{%kpi_data}}');
    }
}
