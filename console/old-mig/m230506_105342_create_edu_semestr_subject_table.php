<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%edu_semestr_subject}}`.
 */
class m230506_105342_create_edu_semestr_subject_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'edu_semestr_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('edu_semestr_subject');
        }

        $this->createTable('{{%edu_semestr_subject}}', [
            'id' => $this->primaryKey(),
            'edu_semestr_id'=>$this->integer()->notNull(),
            'subject_id'=>$this->integer()->notNull(),
            'subject_semestr_id'=>$this->integer()->null(),
            'subject_type_id'=>$this->integer()->null(),
            'faculty_id'=>$this->integer()->null(),
            'direction_id'=>$this->integer()->null(),

            'credit'=>$this->double()->defaultValue(0),

            'auditory_time'=>$this->double()->null(),
            'all_ball_yuklama'=>$this->integer()->null()->defaultValue(0),
            'is_checked'=>$this->integer()->null()->defaultValue(0),
            'max_ball'=>$this->integer()->null()->defaultValue(0),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_edu_semestr_subject_table_edu_semestr_table', 'edu_semestr_subject', 'edu_semestr_id', 'edu_semestr', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_subject_table', 'edu_semestr_subject', 'subject_id', 'subject', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_subject_type_table', 'edu_semestr_subject', 'subject_type_id', 'subject_type', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_faculty_table', 'edu_semestr_subject', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_edu_semestr_subject_table_direction_table', 'edu_semestr_subject', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropForeignKey('mk_edu_semestr_subject_table_edu_semestr_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_subject_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_subject_type_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_faculty_table', 'edu_semestr_subject');
        $this->dropForeignKey('mk_edu_semestr_subject_table_direction_table', 'edu_semestr_subject');

        
        $this->dropTable('{{%edu_semestr_subject}}');
    }
}
