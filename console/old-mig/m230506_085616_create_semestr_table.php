<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%semestr}}`.
 */
class m230506_085616_create_semestr_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'semestr';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('semestr');
        }

        $this->createTable('{{%semestr}}', [
            'id' => $this->primaryKey(),
            'course_id' => $this->integer()->notNull(),
            'type' => $this->integer()->notNull()->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_semestr_table_course_table', 'semestr', 'course_id', 'course', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_semestr_table_course_table', 'semestr');
        $this->dropTable('{{%semestr}}');
    }
}
