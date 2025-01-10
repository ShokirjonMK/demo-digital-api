<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%attend_faculty_statistic}}`.
 */
class m240402_073732_create_attend_faculty_statistic_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'attend_faculty_statistic';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('attend_faculty_statistic');
        }

        $this->createTable('{{%attend_faculty_statistic}}', [
            'id' => $this->primaryKey(),
            'date' => $this->date()->notNull(),
            'faculty_id' => $this->integer()->notNull(),

            'attend_count' => $this->integer()->defaultValue(0),
            'attend_reason_count' => $this->integer()->defaultValue(0),
            'attend_percent' => $this->integer()->defaultValue(0),

            'order' => $this->integer()->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_attend_faculty_statistic_table_faculty_table', 'attend_faculty_statistic', 'faculty_id', 'faculty', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%attend_faculty_statistic}}');
    }
}
