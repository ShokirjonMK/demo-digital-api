<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%homepage_statistic}}`.
 */
class m240110_045728_create_homepage_statistic_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'homepage_statistic';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('homepage_statistic');
        }

        $this->createTable('{{%homepage_statistic}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'role_name' => $this->string(255)->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'date' => $this->integer()->notNull(),
            'json' => $this->json()->null(),
            'is_deleted' => $this->integer()->defaultValue(0)
        ], $tableOptions);
        $this->addForeignKey('mk_homepage_statistic_table_user_table', 'homepage_statistic', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_homepage_statistic_table_edu_year_table', 'homepage_statistic', 'edu_year_id', 'edu_year', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%homepage_statistic}}');
    }
}
