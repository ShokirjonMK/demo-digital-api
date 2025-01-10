<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_content}}`.
 */
class m230621_115024_create_subject_content_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'subject_content';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_content');
        }

        $this->createTable('{{%subject_content}}', [
            'id' => $this->primaryKey(),
            'subject_topic_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->null(),
            'subject_semestr_id' => $this->integer()->null(),
            'user_id' => $this->integer()->null(),
            'teacher_access_id' => $this->integer()->null(),

            'type' => $this->integer()->notNull(),
            'text' => $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
            'description' => $this->text()->null(),
            'file' => $this->string(255)->null(),
            'file_extension' => $this->string(50)->null(),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_subject_content_table_subject_topic_table', 'subject_content', 'subject_topic_id', 'subject_topic', 'id');
        $this->addForeignKey('mk_subject_content_table_subject_table', 'subject_content', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_subject_content_table_subject_semestr_table', 'subject_content', 'subject_semestr_id', 'subject_semestr', 'id');
        $this->addForeignKey('mk_subject_content_table_user_table', 'subject_content', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_subject_content_table_teacher_access_table', 'subject_content', 'teacher_access_id', 'teacher_access', 'id');

    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropForeignKey('mk_subject_content_table_subject_table', 'subject_content');
        $this->dropForeignKey('mk_subject_content_table_subject_topic_table', 'subject_content');
        $this->dropForeignKey('mk_subject_content_table_lang_table', 'subject_content');
        $this->dropForeignKey('mk_subject_content_table_user_table', 'subject_content');
        $this->dropForeignKey('mk_subject_content_table_teacher_access_table', 'subject_content');
        $this->dropTable('{{%subject_content}}');
    }
}
