<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cantract}}`.
 */
class m220729_050935_create_cantract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'contract';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('contract');
        }
        
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%contract}}', [
            'id' => $this->primaryKey(),
            'edu_year_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),
            'edu_form_id' => $this->integer()->notNull(),
            'type'=>$this->integer()->Null(),
            'amount'=>$this->string(255),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);
        // Edu_year
        $this->createIndex('index_cey_comtract_edu_year_id', 'contract', 'edu_year_id');
        $this->addForeignKey('cey_comtract_edu_year_id', 'contract', 'edu_year_id', 'edu_year', 'id');
        // Edu_type
        $this->createIndex('index_cet_comtract_edu_type_id', 'contract', 'edu_type_id');
        $this->addForeignKey('cet_comtract_edu_type_id', 'contract', 'edu_type_id', 'edu_type', 'id');
        // Edu_form
        $this->createIndex('index_cef_comtract_edu_form_id', 'contract', 'edu_form_id');
        $this->addForeignKey('cef_comtract_edu_form_id', 'contract', 'edu_form_id', 'edu_form', 'id');
    }



    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Edu_year
        $this->dropForeignKey('cey_comtract_edu_year_id', 'contract');
        $this->dropIndex('idindex_cey_comtract_edu_year_idd', 'contract');
        // Edu_type
        $this->dropForeignKey('cet_comtract_edu_type_id', 'contract');
        $this->dropIndex('index_cet_comtract_edu_type_id', 'contract');
        // Edu_form
        $this->dropForeignKey('cef_comtract_edu_form_id', 'contract');
        $this->dropIndex('index_cef_comtract_edu_form_id', 'contract');

        $this->dropTable('{{%contract}}');
    }
}
