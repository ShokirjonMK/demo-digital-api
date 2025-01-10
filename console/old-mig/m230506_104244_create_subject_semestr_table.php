<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_semestr}}`.
 */
class m230506_104244_create_subject_semestr_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'subject_semestr';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_semestr');
        }

        $this->createTable('{{%subject_semestr}}', [
            'id' => $this->primaryKey(),
            'subject_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->null(),
            'kafedra_id' => $this->integer()->null(),
            'edu_year_id' => $this->integer()->null(),
            'subject_type_id'=>$this->integer()->null(),
            'edu_form_id'=>$this->integer()->null(),
            'credit' => $this->integer()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_subject_semestr_table_subject_table', 'subject_semestr', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_subject_semestr_table_semestr_table', 'subject_semestr', 'semestr_id', 'semestr', 'id');
        $this->addForeignKey('mk_subject_semestr_table_kafedra_table', 'subject_semestr', 'kafedra_id', 'kafedra', 'id');
        $this->addForeignKey('mk_subject_semestr_table_edu_year_table', 'subject_semestr', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_subject_semestr_table_subject_type_table', 'subject_semestr', 'subject_type_id', 'subject_type', 'id');
        $this->addForeignKey('mk_subject_semestr_table_edu_form_table', 'subject_semestr', 'edu_form_id', 'edu_form', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subject_semestr}}');
    }
}
