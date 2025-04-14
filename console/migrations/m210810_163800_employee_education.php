<?php

use yii\db\Migration;

/**
 * Class m210810_163800_employee_education
 */
class m210810_163800_employee_education extends Migration
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

        $this->createTable('{{%employee_education}}', [
            'id' => $this->primaryKey(),
            'employee_id' => $this->integer()->notNull(),
            'university_id' => $this->integer()->notNull(),
            'specialty_id' => $this->integer(),
            'education' => $this->tinyInteger(1)->notNull(),
            'education_level' => $this->tinyInteger(1)->notNull(),
            'university_finished_at' => $this->timestamp()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%employee_education}}');
    }
}
