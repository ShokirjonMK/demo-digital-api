<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%group}}`.
 */
class m230506_103141_create_group_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'group';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('group');
        }

        $this->createTable('{{%group}}', [
            'id' => $this->primaryKey(),
            'faculty_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'edu_plan_id' => $this->integer()->notNull(),
            'language_id' => $this->integer()->notNull(),
            'unical_name' => $this->string(255)->notNull()->unique(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('mk_group_table_faculty_table', 'group', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_group_table_direction_table', 'group', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_group_table_edu_plan_table', 'group', 'edu_plan_id', 'edu_plan', 'id');
        $this->addForeignKey('mk_group_table_languages_table', 'group', 'language_id', 'languages', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_group_table_faculty_table', 'group');
        $this->dropForeignKey('mk_group_table_direction_table', 'group');
        $this->dropForeignKey('mk_group_table_edu_plan_table', 'group');
        $this->dropForeignKey('mk_group_table_languages_table', 'group');
        $this->dropTable('{{%group}}');
    }
}
