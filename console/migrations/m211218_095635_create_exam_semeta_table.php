<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_semeta}}`.
 */
class m211218_095635_create_exam_semeta_table extends Migration
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
        $this->createTable('{{%exam_semeta}}', [
            'id' => $this->primaryKey(),

            'exam_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'lang_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('esess_exam_smeta_exam', 'exam_semeta', 'exam_id', 'exam', 'id');
        $this->addForeignKey('esl_language', 'exam_semeta', 'lang_id', 'languages', 'id');
        $this->addForeignKey('esta_teacher_access_relection_bm', 'exam_semeta', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('esess_exam_smeta_exam', 'exam_semeta');
        $this->dropForeignKey('esl_language', 'exam_semeta');
        $this->dropForeignKey('esta_teacher_access_relection_bm', 'exam_semeta');
        $this->dropTable('{{%exam_semeta}}');
    }
}
