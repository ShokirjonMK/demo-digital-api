<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%final_exam_group}}`.
 */
class m240117_093514_create_final_exam_group_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'final_exam_group';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('final_exam_group');
        }

        $this->createTable('{{%final_exam_group}}', [
            'id' => $this->primaryKey(),
            'final_exam_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'edu_semestr_subject_id' => $this->integer()->null(),
            'edu_semestr_id' => $this->integer()->null(),
            'edu_plan_id' => $this->integer()->null(),
            'vedomst' => $this->integer()->null(),

            'date' => $this->date()->null(),
            'para_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_final_exam_group_table_final_exam_table', 'final_exam_group', 'final_exam_id', 'final_exam', 'id');
        $this->addForeignKey('mk_final_exam_group_table_group_table', 'final_exam_group', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_final_exam_group_table_edu_semestr_subject_table', 'final_exam_group', 'edu_semestr_subject_id', 'edu_semestr_subject', 'id');
        $this->addForeignKey('mk_final_exam_group_table_edu_semestr_table', 'final_exam_group', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_final_exam_group_table_edu_plan_table', 'final_exam_group', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_final_exam_group_table_para_table', 'final_exam_group', 'para_id', 'para', 'id');
        $this->addForeignKey('mk_final_exam_group_table_user_table', 'final_exam_group', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%final_exam_group}}');
    }
}
