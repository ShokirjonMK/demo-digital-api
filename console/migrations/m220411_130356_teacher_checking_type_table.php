<?php

use yii\db\Migration;

/**
 * Class m220411_130356_teacher_checking_type_table
 */
class m220411_130356_teacher_checking_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'teacher_checking_type';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('teacher_checking_type');
        }

        $tableOptions = null;
       
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%teacher_checking_type}}', [
            'id' => $this->primaryKey(),

            'edu_year_id' => $this->integer()->notNull(),
            'semestr_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger(1)->defaultValue(1),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->addForeignKey('tchtey_teacher_checking_type_edu_year_id', 'teacher_checking_type', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('tchtey_teacher_checking_type_semestr_id', 'teacher_checking_type', 'semestr_id', 'semestr', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('tchtey_teacher_checking_type_edu_year_id', 'teacher_checking_type');
        $this->dropForeignKey('tchtey_teacher_checking_type_semestr_id', 'teacher_checking_type');

        $this->dropTable('teacher_checking_types');
    }
}
