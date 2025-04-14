<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%hostel_student_room}}`.
 */
class m230615_115512_create_hostel_student_room_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'hostel_student_room';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('hostel_student_room');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%hostel_student_room}}', [
            'id' => $this->primaryKey(),
            'student_id' => $this->integer(11)->notNull(),
            'room_id' => $this->integer(11)->notNull(),

            'faculty_id' => $this->integer(11)->null(),
            'edu_year_id' => $this->integer(11)->null(),
            'edu_plan_id' => $this->integer(11)->null(),
            'payed' => $this->double()->null(),

            'is_contract' => $this->tinyInteger(1)->defaultValue(1),
            'is_free' => $this->tinyInteger(1)->defaultValue(0),

            'archived' => $this->tinyInteger(1)->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->null()->defaultValue(0),
            'updated_by' => $this->integer()->null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->null()->defaultValue(0),
        ], $tableOptions);;
        $this->addForeignKey('mk_hostel_student_room_room_id', 'hostel_student_room', 'room_id', 'room', 'id');
        $this->addForeignKey('mk_hostel_student_room_student_id', 'hostel_student_room', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_hostel_student_room_faculty_id', 'hostel_student_room', 'faculty_id', 'faculty', 'id');
        $this->addForeignKey('mk_hostel_student_room_edu_year_id', 'hostel_student_room', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_hostel_student_room_edu_plan_id', 'hostel_student_room', 'edu_plan_id', 'edu_plan', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_hostel_student_room_room_id', 'hostel_student_room');
        $this->dropForeignKey('mk_hostel_student_room_student_id', 'hostel_student_room');
        $this->dropForeignKey('mk_hostel_student_room_faculty_id', 'hostel_student_room');
        $this->dropForeignKey('mk_hostel_student_room_edu_year_id', 'hostel_student_room');
        $this->dropForeignKey('mk_hostel_student_room_edu_plan_id', 'hostel_student_room');
        $this->dropTable('{{%hostel_student_room}}');
    }
}
