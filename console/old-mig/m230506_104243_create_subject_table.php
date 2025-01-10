<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject}}`.
 */
class m230506_104243_create_subject_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject');
        }

        $this->createTable('{{%subject}}', [
            'id' => $this->primaryKey(),
            'kafedra_id' => $this->integer()->notNull(),
            'edu_type_id' => $this->integer()->notNull(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_subject_table_kafedra_table', 'subject', 'kafedra_id', 'kafedra', 'id');
        $this->addForeignKey('mk_subject_table_edu_type_table', 'subject', 'edu_type_id', 'edu_type', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_subject_table_kafedra_table', 'subject');
        $this->dropForeignKey('mk_subject_table_semestr_table', 'subject');
        $this->dropForeignKey('mk_subject_table_parent_table', 'subject');
        $this->dropForeignKey('mk_subject_table_subject_type_table', 'subject');
        $this->dropForeignKey('mk_subject_table_edu_type_table', 'subject');
        $this->dropForeignKey('mk_subject_table_edu_form_table', 'subject');
        $this->dropTable('{{%subject}}');
    }
}
