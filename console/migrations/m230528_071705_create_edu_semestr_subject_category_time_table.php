<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_semestr_subject_category_time}}`.
 */
class m230528_071705_create_edu_semestr_subject_category_time_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'edu_semestr_subject_category_time';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('edu_semestr_subject_category_time');
        }
        $this->createTable('{{%edu_semestr_subject_category_time}}', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->notNull(),
            'hours' => $this->integer()->notNull(),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'edu_semestr_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey('mk_e_s_subject_category_time_table_e_s_subject_table', 'edu_semestr_subject_category_time', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_e_s_subject_category_time_table_subject_category_table', 'edu_semestr_subject_category_time', 'subject_category_id', 'subject_category', 'id');
        $this->addForeignKey('mk_e_s_subject_category_time_table_edu_semestr_table', 'edu_semestr_subject_category_time', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_e_s_subject_category_time_table_subject_table', 'edu_semestr_subject_category_time', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropForeignKey('mk_e_s_subject_category_time_table_e_s_subject_table', 'edu_semestr_subject_category_time');
        $this->dropForeignKey('mk_e_s_subject_category_time_table_subject_category_table', 'edu_semestr_subject_category_time');
        $this->dropForeignKey('mk_e_s_subject_category_time_table_edu_semestr_table', 'edu_semestr_subject_category_time');
        $this->dropForeignKey('mk_e_s_subject_category_time_table_subject_table', 'edu_semestr_subject_category_time');
        $this->dropTable('{{%edu_semestr_subject_category_time}}');
    }
}
