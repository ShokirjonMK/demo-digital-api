<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%subject_topic}}`.
 */
class m230618_071117_create_subject_topic_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'subject_topic';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('subject_topic');
        }

        $this->createTable('{{%subject_topic}}', [
            'id' => $this->primaryKey(),
            'subject_semestr_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),
            'lang_id' => $this->integer()->notNull(),
            'subject_category_id' => $this->integer()->null()->comment('Fan turlari boyicha topic uchun'),
            'name' => $this->text()->notNull(),
            'description' => $this->text()->null(),

            'order'=> $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);
        $this->addForeignKey('mk_subject_topic_table_subject_semestr_table', 'subject_topic', 'subject_semestr_id', 'subject_semestr', 'id');
        $this->addForeignKey('mk_subject_topic_table_subject_table', 'subject_topic', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_subject_topic_table_lang_table', 'subject_topic', 'lang_id', 'language', 'id');
        $this->addForeignKey('mk_subject_topic_table_subject_category_table', 'subject_topic', 'subject_category_id', 'subject_category', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_subject_topic_table_teacher_access_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_parent_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_subject_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_lang_table', 'subject_topic');
        $this->dropForeignKey('mk_subject_topic_table_subject_category_table', 'subject_topic');
        $this->dropTable('{{%subject_topic}}');
    }
}
