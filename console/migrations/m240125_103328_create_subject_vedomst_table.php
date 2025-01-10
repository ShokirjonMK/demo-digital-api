<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_vedomst}}`.
 */
class m240125_103328_create_subject_vedomst_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'subject_vedomst';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_vedomst');
        }

        $this->createTable('{{%subject_vedomst}}', [
            'id' => $this->primaryKey(),
            'edu_semestr_subject_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'edu_semestr_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_subject_vedomst_table_edu_semestr_subject_table', 'subject_vedomst', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_subject_vedomst_table_edu_plan_table', 'subject_vedomst', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_subject_vedomst_table_edu_semestr_table', 'subject_vedomst', 'edu_semestr_id', 'edu_semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%subject_vedomst}}');
    }
}
