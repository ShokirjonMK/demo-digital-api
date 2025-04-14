<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%teacher_plan}}`.
 */
class m231008_041150_create_teacher_plan_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'student_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student_subject');
        }

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%teacher_plan}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'subject_id' => $this->integer(11)->notNull(),
            'edu_year_id' => $this->integer(11)->notNull(),
            'semestr_type' => $this->tinyInteger(1)->defaultValue(1)->comment("1 kuz 2 bahor"),
            'course_id' => $this->integer(11)->null(),
            'semestr_id' => $this->integer(11)->null(),

            'student_count' => $this->integer(11)->null()->comment("Talaba soni"),
            'student_count_plan' => $this->integer(11)->null()->comment("Talaba soni reja"),

            'lecture' => $this->integer(11)->null()->comment("ma'ruza mashg'uloti"),
            'lecture_plan' => $this->integer(11)->null()->comment("ma'ruza mashg'uloti reja"),

            'seminar' => $this->integer(11)->null()->comment("Seminar mashg'uloti"),
            'seminar_plan' => $this->integer(11)->null()->comment("Seminar mashg'uloti reja"),

            'practical' => $this->integer(11)->null()->comment("Amaliy mashg'ulot"),
            'practical_plan' => $this->integer(11)->null()->comment("Amaliy mashg'ulot reja"),

            'labarothoria' => $this->integer(11)->null()->comment("Labarotoriya mashg'uloti"),
            'labarothoria_plan' => $this->integer(11)->null()->comment("Labarotoriya mashg'uloti reja"),

            'advice' => $this->integer(11)->null()->comment("Maslahatlar o'tkazish"),
            'advice_plan' => $this->integer(11)->null()->comment("Maslahatlar o'tkazish reja"),

            'prepare' => $this->integer(11)->null()->comment("Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish"),
            'prepare_plan' => $this->integer(11)->null()->comment("Ma'ruza va seminar (amaliy) mashg'ulotlarga tayyorgarlik ko'rish reja"),

            'checking' => $this->integer(11)->null()->comment("Oraliq va yakuniy nazoratlarni tekshirish"),
            'checking_plan' => $this->integer(11)->null()->comment("Oraliq va yakuniy nazoratlarni tekshirish reja"),

            'checking_appeal' => $this->integer(11)->null()->comment("Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish"),
            'checking_appeal_plan' => $this->integer(11)->null()->comment("Yakuniy nazorat turi bo'yicha qo'yilgan balldan norozi bo'lgan talabaning apellyasiya shikoyati ko'rib chiqish bo'yicha apellyasiya komissiyasi a'zosi sifatida ishtirok etish reja"),

            'lead_practice' => $this->integer(11)->null()->comment("Bakalavriat talabalari amaliyotiga rahbarlik qilish va b."),
            'lead_practice_plan' => $this->integer(11)->null()->comment("Bakalavriat talabalari amaliyotiga rahbarlik qilish va b. reja"),

            'lead_graduation_work' => $this->integer(11)->null()->comment("Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish"),
            'lead_graduation_work_plan' => $this->integer(11)->null()->comment("Bakalavriat talabalarining bitiruv malakaviy ishiga rahbarlik qilish, xulosalar yozish reja"),

            'dissertation_advicer' => $this->integer(11)->null()->comment("Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish"),
            'dissertation_advicer_plan' => $this->integer(11)->null()->comment("Magistratura talabasining ilmiy tadqiqot ishi va magistrlik dissertasiyasiga ilmiy maslahatchilik qilish reja"),

            'doctoral_consultation' => $this->integer(11)->null()->comment("TDYU doktorantiga ilmiy maslahatchilik qilish"),
            'doctoral_consultation_plan' => $this->integer(11)->null()->comment("TDYU doktorantiga ilmiy maslahatchilik qilish reja"),

            'supervisor_exam' => $this->integer(11)->null()->comment("Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish"),
            'supervisor_exam_plan' => $this->integer(11)->null()->comment("Yakuniy nazorat yozma imtihonlarida nazoratchi sifatida ishtirok etish reja"),

            'kazus_input' => $this->integer(11)->null()->comment("Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish"),
            'kazus_input_plan' => $this->integer(11)->null()->comment("Talabalar bilimini aniqlash bo'yicha nazorat turlari uchun mantiqiy savollar, muammoli masalalar (kazuslar) ishlab chiqish reja"),

            'legal_clinic' => $this->integer(11)->null()->comment("Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish"),
            'legal_clinic_plan' => $this->integer(11)->null()->comment("Toshkent davlat yuridik universiteti yuridik klinikasi faoliyatida ishtirok etish reja"),

            'final_attestation' => $this->integer(11)->null()->comment("Yakuniy davlat attestasiyasini o'tkazish"),
            'final_attestation_plan' => $this->integer(11)->null()->comment("Yakuniy davlat attestasiyasini o'tkazish reja"),


            'description' => $this->text()->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'is_deleted' => $this->tinyInteger(1)->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),

        ], $tableOptions);
        $this->addForeignKey('tpu_teacher_plan_user', 'teacher_plan', 'user_id', 'users', 'id');
        $this->addForeignKey('tps_teacher_plan_subject', 'teacher_plan', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys
        $this->dropForeignKey('tpu_teacher_plan_user', 'teacher_plan');
        $this->dropForeignKey('tps_teacher_plan_subject', 'teacher_plan');

        // Drop the 'teacher_plan' table
        $this->dropTable('{{%teacher_plan}}');
    }
}
