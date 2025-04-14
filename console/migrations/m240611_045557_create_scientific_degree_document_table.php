<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%scientific_degree_document}}`.
 */
class m240611_045557_create_scientific_degree_document_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'scientific_degree_document';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('scientific_degree_document');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%scientific_degree_document}}', [
            'id' => $this->primaryKey(),
            'academic_degree_id' => $this->integer()->null(),
            'degree_id' => $this->integer()->null(),
            'name' => $this->text()->null(),
            'scientific_specialization_id' => $this->integer()->notNull(),
            'council_number' => $this->string(255)->null(),
            'council_name' => $this->text()->null(),
            'protection_date' => $this->date()->Null(),
            'performed_organization' => $this->string(255)->null(),
            'leader_info' => $this->text()->null(),
            'independent' => $this->string(255)->null(),
            'base' => $this->string(255)->null(),
            'autoreferat_file' => $this->string(255)->null(),
            'diploma_number' => $this->string(255)->null(),
            'diploma_file' => $this->string(255)->null(),
            'dissertation_file' => $this->string(255)->null(),
            'attestat_raqami' => $this->string(255)->null(),
            'organization_recommended' => $this->string(255)->null(),
            'oak_order_date' => $this->date()->null(),
            'user_id' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull()->defaultValue(0),
            'updated_at' => $this->integer()->notNull()->defaultValue(0),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'archived' => $this->integer()->notNull()->defaultValue(0)
        ], $tableOptions);

        $this->addForeignKey('sdd_user_id', 'scientific_degree_document', 'user_id', 'users', 'id');
        $this->addForeignKey('sdd_academic_degree', 'scientific_degree_document', 'academic_degree_id', 'academic_degree', 'id');
        $this->addForeignKey('sdd_degree', 'scientific_degree_document', 'degree_id', 'degree', 'id');
        $this->addForeignKey('sdd_scientific_specialization', 'scientific_degree_document', 'scientific_specialization_id', 'scientific_specialization', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('sdd_user_id', 'scientific_degree_document');
        $this->dropForeignKey('sdd_academic_degree', 'scientific_degree_document');
        $this->dropForeignKey('sdd_degree', 'scientific_degree_document');
        $this->dropForeignKey('sdd_scientific_specialization', 'scientific_degree_document');

        $this->dropTable('{{%scientific_degree_document}}');
    }
}
