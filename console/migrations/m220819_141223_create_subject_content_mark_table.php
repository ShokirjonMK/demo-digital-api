<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_content_mark}}`.
 */
class m220819_141223_create_subject_content_mark_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'subject_content_mark';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_content_mark');
        }
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%subject_content_mark}}', [
            'id' => $this->primaryKey(),

            'ball' => $this->double()->Null(),
            'user_id' => $this->integer(11)->Null(),
            'teacher_access_id' => $this->integer(11)->Null(),
            'subject_topic_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->Null(),
            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('subject_content_mark_user_id', 'subject_content_mark', 'user_id', 'users', 'id');
        $this->addForeignKey('subject_content_mark_teacher_access_id', 'subject_content_mark', 'teacher_access_id', 'teacher_access', 'id');
        $this->addForeignKey('subject_content_mark_subject_topic_id', 'subject_content_mark', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('subject_content_mark_subject_id', 'subject_content_mark', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('subject_content_mark_user_id', 'subject_content_mark');
        $this->dropForeignKey('subject_content_mark_teacher_access_id', 'subject_content_mark');
        $this->dropForeignKey('subject_content_mark_subject_topic_id', 'subject_content_mark');
        $this->dropForeignKey('subject_content_mark_subject_id', 'subject_content_mark');

        $this->dropTable('{{%subject_content_mark}}');
    }
}
