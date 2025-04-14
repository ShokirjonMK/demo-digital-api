<?php

use yii\db\Migration;

/**
 * Class m211104_055719_insert_smester_course
 */
class m211104_055719_insert_smester_course extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Yii::$app->db->createCommand()->truncateTable('course')->execute();
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("TRUNCATE table  `course`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");

        $this->insert('{{%course}}', [
            'id' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 1, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 1,
            'name' => '1',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '1',
        ]);

        $this->insert('{{%course}}', [
            'id' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 2, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 2,
            'name' => '2',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '2',
        ]);

        $this->insert('{{%course}}', [
            'id' => 3,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 3, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 3,
            'name' => '3',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '3',
        ]);

        $this->insert('{{%course}}', [
            'id' => 4,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 4, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 4,
            'name' => '4',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '4',
        ]);

        $this->insert('{{%course}}', [
            'id' => 5,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 5, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 5,
            'name' => '5',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '5',
        ]);

        $this->insert('{{%course}}', [
            'id' => 6,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 6, 'table_name' => 'course', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 6,
            'name' => '6',
            'table_name' => 'course',
            'language' => 'uz',
            'description' => '6',
        ]);


        // Yii::$app->db->createCommand()->truncateTable('semestr')->execute();
        $this->execute("SET FOREIGN_KEY_CHECKS = 0;");
        $this->execute("TRUNCATE table  `semestr`;");
        $this->execute("SET FOREIGN_KEY_CHECKS = 1;");
        $this->insert('{{%semestr}}', [
            'course_id' => 1,
            'id' => 1,
            'type' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 1, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 1,
            'name' => '1',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '1',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 1,
            'id' => 2,
            'type' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 2, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 2,
            'name' => '2',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '2',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 2,
            'id' => 3,
            'type' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 3, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 3,
            'name' => '3',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '3',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 2,
            'id' => 4,
            'type' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 4, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 4,
            'name' => '4',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '4',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 3,
            'id' => 5,
            'type' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 5, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 5,
            'name' => '5',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '5',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 3,
            'id' => 6,
            'type' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 6, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 6,
            'name' => '6',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '6',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 4,
            'id' => 7,
            'type' => 1,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 7, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 7,
            'name' => '7',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '7',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 4,
            'id' => 8,
            'type' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 8, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 8,
            'name' => '8',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '8',
        ]);

        $this->insert(
            '{{%semestr}}',
            [
                'course_id' => 5,
                'id' => 9,
                'type' => 1,
                'updated_at' => 0,
                'created_at' => 0,
                'status' => 1,
            ]
        );

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 9, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 9,
            'name' => '9',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '9',
        ]);

        $this->insert('{{%semestr}}', [
            'course_id' => 5,
            'id' => 10,
            'type' => 2,
            'updated_at' => 0,
            'created_at' => 0,
            'status' => 1,
        ]);

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 10, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 10,
            'name' => '10',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '10',
        ]);

        $this->insert(
            '{{%semestr}}',
            [
                'course_id' => 6,
                'id' => 11,
                'type' => 1,
                'updated_at' => 0,
                'created_at' => 0,
                'status' => 1,
            ]
        );

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 11, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 11,
            'name' => '11',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '11',
        ]);

        $this->insert(
            '{{%semestr}}',
            [
                'course_id' => 6,
                'id' => 12,
                'type' => 2,
                'updated_at' => 0,
                'created_at' => 0,
                'status' => 1,
            ]
        );

        Yii::$app->db->createCommand()->delete('translate', ['model_id' => 12, 'table_name' => 'semestr', 'language' => 'uz'])->execute();
        $this->insert('{{%translate}}', [
            'model_id' => 12,
            'name' => '12',
            'table_name' => 'semestr',
            'language' => 'uz',
            'description' => '12',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211104_055719_insert_smester_course cannot be reverted.\n";

        return false;
    }
}
