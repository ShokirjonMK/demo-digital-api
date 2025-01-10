<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%timetable_student}}`.
 */
class m240217_094357_create_timetable_student_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'timetable_student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('timetable_student');
        }

        $this->createTable('{{%timetable_student}}', [
            'id' => $this->primaryKey(),
//            'timetable_id' => $this->integer()->notNull(),
            'ids_id' => $this->integer()->notNull(),
            'group_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'student_user_id' => $this->integer()->notNull(),
            'group_type' => $this->integer()->defaultValue(1),

            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

//        $this->addForeignKey('mk_timetable_student_table_timetable_table', 'timetable_student', 'timetable_id', 'timetable', 'id');
        $this->addForeignKey('mk_timetable_student_table_timetable_ids_table', 'timetable_student', 'ids_id', 'timetable', 'ids');
        $this->addForeignKey('mk_timetable_student_table_group_table', 'timetable_student', 'group_id', 'group', 'id');
        $this->addForeignKey('mk_timetable_student_table_student_table', 'timetable_student', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_timetable_student_table_student_user_table', 'timetable_student', 'student_user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */

    public function safeDown()
    {
        $this->dropTable('{{%timetable_student}}');
    }
}
