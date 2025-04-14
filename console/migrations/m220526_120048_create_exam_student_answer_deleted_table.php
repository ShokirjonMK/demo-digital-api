<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_answer_deleted}}`.
 */
class m220526_120048_create_exam_student_answer_deleted_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'exam_student_answer_deleted';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_answer_deleted');
        }

        $this->createTable('{{%exam_student_answer_deleted}}', [
            'id' => $this->primaryKey(),
            'file' => $this->string(255)->Null(),
            'exam_student_answer_id' => $this->integer()->Null(),
            'exam_student_id' => $this->integer()->Null(),
            'parent_id' => $this->integer()->Null(),
            'exam_id' => $this->integer()->Null(),
            'question_id' => $this->integer()->Null(),
            'student_id' => $this->integer()->Null(),
            'option_id' => $this->integer()->Null(),
            'answer' => $this->text()->Null(),
            'teacher_conclusion' => $this->text()->Null(),
            'max_ball' => $this->double()->Null(),
            'ball' => $this->double()->Null(),
            'teacher_access_id' => $this->integer()->Null(),
            'attempt' => $this->integer()->defaultValue(1)->comment("Nechinchi marta topshirayotgani"),
            'type' => $this->tinyInteger(1)->Null()->comment("1-savol, 2-test, 3-another"),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->Null()->defaultValue(0),
            'updated_by' => $this->integer()->Null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->Null()->defaultValue(0),


        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student_answer_deleted}}');
    }
}
