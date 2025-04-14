<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_appeal_semeta}}`.
 */
class m220703_085729_create_exam_appeal_semeta_table extends Migration
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
        $this->createTable('{{%exam_appeal_semeta}}', [
            'id' => $this->primaryKey(),

            'exam_id' => $this->integer()->notNull(),
            'lang_id' => $this->integer()->notNull(),
            'teacher_access_id' => $this->integer()->notNull(),
            'count' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('eas_exam_appeal_semeta_exam_id', 'exam_appeal_semeta', 'exam_id', 'exam', 'id');
        $this->addForeignKey('eas_exam_appeal_semeta_lang_id', 'exam_appeal_semeta', 'lang_id', 'languages', 'id');
        $this->addForeignKey('eas_exam_appeal_semeta_teacher_access_id', 'exam_appeal_semeta', 'teacher_access_id', 'teacher_access', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('eas_exam_appeal_semeta_exam_id', 'exam_appeal_semeta');
        $this->dropForeignKey('eas_exam_appeal_semeta_lang_id', 'exam_appeal_semeta');
        $this->dropForeignKey('eas_exam_appeal_semeta_teacher_access_id', 'exam_appeal_semeta');

        $this->dropTable('{{%exam_appeal_semeta}}');
    }
}
