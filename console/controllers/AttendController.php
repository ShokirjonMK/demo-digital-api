<?php

namespace console\controllers;

use common\models\model\Attend;
use common\models\model\Holiday;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentAttend;
use common\models\model\StudentTimeTable;
use common\models\model\TimeTable;
use common\models\model\Turniket;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class AttendController extends Controller
{

    public function actionIndex()
    {
        // print_r(date('Y-m-d H:i:s'));
        // die();

        $today = date('Y-m-d', strtotime('-1 day'));

        // 0. Dars bo'lgan sanalarni olish
        $timeTables = TimeTable::find()
            ->select('time_table.*')
            ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
            ->leftJoin('edu_plan', 'time_table.edu_plan_id = edu_plan.id')
            ->where([
                'time_table.is_deleted' => 0,
                'time_table.archived' => 0,
                'time_table.week_id' => date('N', strtotime($today))
            ])
            ->andWhere(['<=', 'edu_semestr.start_date', $today])
            ->andWhere(['>=', 'edu_semestr.end_date', $today])
            ->andWhere(['not in', 'edu_plan.edu_form_id', [8, 6]])
            // ->andWhere(['in', 'time_table.id', [41584]])
            ->all();

        foreach ($timeTables as $timeTable) {

            // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
            $result = $this->isHoliday($today);
            if ($result['status'] == 0) {
                continue; // Agar bugun bayram kuni bo'lsa, o'tib ketamiz
            }
            $today = $result['date'];

            // 2. O'sha dars jadvaliga yozilgan talabalarni olish
            $studentTimeTables = StudentTimeTable::find()
                ->where(['time_table_id' => $timeTable->id])
                ->all();

            $studentIds = array_map(fn($st) => $st->student_id, $studentTimeTables);

            if (empty($studentIds)) {
                continue;
            }

            // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish
            $turniketUserIds = Turniket::find()
                ->select('user_id')
                ->where(['in', 'user_id', Student::find()->select('user_id')->where(['in', 'id', $studentIds])])
                ->andWhere(['date' => $today])
                ->andWhere(['type' => Turniket::TYPE_OQUV])
                ->column();

            $turniketStudentIds = Student::find()
                ->select('id')
                ->where(['in', 'user_id', $turniketUserIds])
                ->column();

            // 4. Kirib kelmagan talabalarni aniqlash
            $absentStudents = array_diff($studentIds, $turniketStudentIds);
            // print_r(['absentStudents' => $absentStudents]);
            // print_r(['turniketStudentIds' => $turniketStudentIds]);
            // // print_r(['sql' => sqlraw($turniketUserIds)]);
            // print_r(['studentIds' => $studentIds]);
            // print_r(['today' => $today]);


            if (!empty($absentStudents)) {
                // 5. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
                $attend = Attend::find()
                    ->where(['time_table_id' => $timeTable->id, 'date' => $today])
                    ->one();

                if (!$attend) {
                    // Agar yo'qlama jadvali mavjud bo'lmasa, yangi yozuv yaratamiz
                    $attend = new Attend();
                    $attend->time_table_id = $timeTable->id;
                    $attend->date = $today;
                    // $attend->student_ids = ((array)json_decode(json_encode($absentStudents)));
                    $attend->status = 2; // Yo'qlama

                    $attend->save();
                }
                // else {
                //     // Agar mavjud bo'lsa, `student_ids` ustunini yangilaymiz
                //     $existingStudentIds = $attend->student_ids;
                //     $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
                //     if (!empty($newAbsentStudents)) {
                //         $attend->student_ids = ((array)json_decode(json_encode(array_merge($existingStudentIds, $newAbsentStudents))));
                //         $attend->save();
                //     }
                // }

                // 6. StudentAttend jadvaliga yozish (faqat yangi talabalar uchun)
                foreach ($absentStudents as $studentId) {
                    $studentAttend = StudentAttend::findOne([
                        'student_id' => $studentId,
                        'attend_id' => $attend->id,
                        'date' => $today,
                        'time_table_id' => $timeTable->id,
                    ]);

                    if (!$studentAttend) {
                        $studentAttend = new StudentAttend();
                        $studentAttend->student_id = $studentId;
                        $studentAttend->attend_id = $attend->id;
                        $studentAttend->date = $today;
                        $studentAttend->time_table_id = $timeTable->id;
                        $studentAttend->status = StudentAttend::STATUS_AVTOBOT;
                        $studentAttend->save();
                    }
                    // else {
                    //     $studentAttend->status = StudentAttend::STATUS_AVTOBOT;
                    //     $studentAttend->save();
                    // }
                }
            }
        }
        print_r($today);

        return ExitCode::OK;
    }
    public function actionDate($date)
    {
        // print_r(date('Y-m-d H:i:s'));
        // die();

        $today = date('Y-m-d', strtotime($date));

        // 0. Dars bo'lgan sanalarni olish
        $timeTables = TimeTable::find()
            ->select('time_table.*')
            ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
            ->where([
                'time_table.is_deleted' => 0,
                'time_table.archived' => 0,
                'time_table.week_id' => date('N', strtotime($today))
            ])
            ->andWhere(['<=', 'edu_semestr.start_date', $today])
            ->andWhere(['>=', 'edu_semestr.end_date', $today])
            // ->andWhere(['in', 'time_table.id', [41584]])
            ->all();

        foreach ($timeTables as $timeTable) {

            // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
            $result = $this->isHoliday($today);
            if ($result['status'] == 0) {
                continue; // Agar bugun bayram kuni bo'lsa, o'tib ketamiz
            }
            $today = $result['date'];

            // 2. O'sha dars jadvaliga yozilgan talabalarni olish
            $studentTimeTables = StudentTimeTable::find()
                ->where(['time_table_id' => $timeTable->id])
                ->all();

            $studentIds = array_map(fn($st) => $st->student_id, $studentTimeTables);

            if (empty($studentIds)) {
                continue;
            }

            // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish
            $turniketUserIds = Turniket::find()
                ->select('user_id')
                ->where(['in', 'user_id', Student::find()->select('user_id')->where(['in', 'id', $studentIds])])
                ->andWhere(['date' => $today])
                ->andWhere(['type' => Turniket::TYPE_OQUV])
                ->column();

            $turniketStudentIds = Student::find()
                ->select('id')
                ->where(['in', 'user_id', $turniketUserIds])
                ->column();

            // 4. Kirib kelmagan talabalarni aniqlash
            $absentStudents = array_diff($studentIds, $turniketStudentIds);
            // print_r(['absentStudents' => $absentStudents]);
            // print_r(['turniketStudentIds' => $turniketStudentIds]);
            // // print_r(['sql' => sqlraw($turniketUserIds)]);
            // print_r(['studentIds' => $studentIds]);
            // print_r(['today' => $today]);


            if (!empty($absentStudents)) {
                // 5. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
                $attend = Attend::find()
                    ->where(['time_table_id' => $timeTable->id, 'date' => $today])
                    ->one();

                if (!$attend) {
                    // Agar yo'qlama jadvali mavjud bo'lmasa, yangi yozuv yaratamiz
                    $attend = new Attend();
                    $attend->time_table_id = $timeTable->id;
                    $attend->date = $today;
                    // $attend->student_ids = ((array)json_decode(json_encode($absentStudents)));
                    $attend->status = 2; // Yo'qlama

                    $attend->save();
                }
                // else {
                //     // Agar mavjud bo'lsa, `student_ids` ustunini yangilaymiz
                //     $existingStudentIds = $attend->student_ids;
                //     $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
                //     if (!empty($newAbsentStudents)) {
                //         $attend->student_ids = ((array)json_decode(json_encode(array_merge($existingStudentIds, $newAbsentStudents))));
                //         $attend->save();
                //     }
                // }

                // 6. StudentAttend jadvaliga yozish (faqat yangi talabalar uchun)
                foreach ($absentStudents as $studentId) {
                    $studentAttend = StudentAttend::findOne([
                        'student_id' => $studentId,
                        'attend_id' => $attend->id,
                        'date' => $today,
                        'time_table_id' => $timeTable->id,
                    ]);

                    if (!$studentAttend) {
                        $studentAttend = new StudentAttend();
                        $studentAttend->student_id = $studentId;
                        $studentAttend->attend_id = $attend->id;
                        $studentAttend->date = $today;
                        $studentAttend->time_table_id = $timeTable->id;
                        $studentAttend->status = StudentAttend::STATUS_AVTOBOT;
                        $studentAttend->save();
                    } else {
                        $studentAttend->status = StudentAttend::STATUS_AVTOBOT;
                        $studentAttend->save();
                    }
                }
            }
        }
        print_r($today);
        return ExitCode::OK;
    }

    // public function actionIndexGptChich()
    // {
    //     $today = date('Y-m-d');

    //     // 0. Dars bo'lgan sanalarni olish (`subject_id` va `subject_category_id` qo'shildi)
    //     $timeTables = TimeTable::find()
    //         ->select('time_table.id, time_table.time_option_id, time_table.edu_year_id, time_table.edu_semester_id, time_table.subject_id, time_table.subject_category_id')
    //         ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
    //         ->where([
    //             'time_table.is_deleted' => 0,
    //             'time_table.archived' => 0,
    //             'time_table.week_id' => date('N', strtotime($today))
    //         ])
    //         ->andWhere(['<=', 'edu_semestr.start_date', $today])
    //         ->andWhere(['>=', 'edu_semestr.end_date', $today])
    //         ->andWhere(['in', 'time_table.id', [41584]])
    //         ->asArray()
    //         ->all();

    //     if (empty($timeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
    //     $result = $this->isHoliday($today);
    //     if ($result['status'] == 0) {
    //         return ExitCode::OK; // Agar bugun bayram kuni bo'lsa, chiqamiz
    //     }
    //     $today = $result['date'];

    //     $timeTableIds = array_column($timeTables, 'id');

    //     // 2. O'sha dars jadvaliga yozilgan barcha talabalarni bitta so‘rov bilan olish
    //     $studentTimeTables = StudentTimeTable::find()
    //         ->select(['time_table_id', 'student_id'])
    //         ->where(['time_table_id' => $timeTableIds])
    //         ->asArray()
    //         ->all();

    //     if (empty($studentTimeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // Talabalarni guruhlash (time_table_id bo'yicha)
    //     $studentsByTimeTable = [];
    //     foreach ($studentTimeTables as $stt) {
    //         $studentsByTimeTable[$stt['time_table_id']][] = $stt['student_id'];
    //     }

    //     // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish (bulk so‘rov)
    //     $turniketUserIds = Turniket::find()
    //         ->select('user_id')
    //         ->where(['in', 'user_id', Profile::find()->select('user_id')->where(['in', 'user_id', array_column($studentTimeTables, 'student_id')])])
    //         ->andWhere(['date' => $today])
    //         ->indexBy('user_id')
    //         ->asArray()
    //         ->all();

    //     $turniketUserIds = array_keys($turniketUserIds);

    //     // 4. Attend yozuvlarini bulk olish
    //     $attends = Attend::find()
    //         ->where(['date' => $today, 'time_table_id' => $timeTableIds])
    //         ->indexBy('time_table_id')
    //         ->all();

    //     $newStudentAttendRecords = [];

    //     foreach ($studentsByTimeTable as $timeTableId => $studentIds) {
    //         // 5. Kirib kelmagan talabalarni aniqlash
    //         $absentStudents = array_diff($studentIds, $turniketUserIds);

    //         if (empty($absentStudents)) {
    //             continue;
    //         }

    //         // `subject_id` va `subject_category_id` ni olish uchun `TimeTable` ma'lumotlarini topamiz
    //         $timeTableInfo = array_filter($timeTables, fn($tt) => $tt['id'] == $timeTableId);
    //         $timeTableInfo = reset($timeTableInfo); // Birinchi mos yozuvni olish

    //         // 6. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
    //         if (!isset($attends[$timeTableId])) {
    //             $attend = new Attend();
    //             $attend->time_table_id = $timeTableId;
    //             $attend->date = $today;
    //             // $attend->student_ids = json_encode(array_values($absentStudents)); // JSON ko'rinishida saqlash
    //             // $attend->student_ids = array_values($absentStudents);
    //             $attend->student_ids = array_map('intval', array_values($absentStudents));
    //             $attend->status = 2; // Yo'qlama
    //             $attend->save();
    //             $attends[$timeTableId] = $attend; // Keyingi ishlash uchun cache'ga saqlaymiz
    //         } else {
    //             $attend = $attends[$timeTableId];
    //             // $existingStudentIds = json_decode($attend->student_ids, true) ?? [];
    //             $existingStudentIds = $attend->student_ids ?? [];
    //             $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
    //             if (!empty($newAbsentStudents)) {
    //                 // $attend->student_ids = json_encode(array_merge($existingStudentIds, $newAbsentStudents));
    //                 // $attend->student_ids = array_merge($existingStudentIds, $newAbsentStudents);
    //                 $attend->student_ids = array_map('intval', array_merge($existingStudentIds, $newAbsentStudents));
    //                 $attend->save();
    //             }
    //         }

    //         // 7. StudentAttend jadvaliga yozish (bulk insert)
    //         foreach ($absentStudents as $studentId) {
    //             $newStudentAttendRecords[] = [
    //                 'student_id' => $studentId,
    //                 'attend_id' => $attend->id,
    //                 'date' => $today,
    //                 'time_table_id' => $timeTableId,
    //                 'subject_id' => $timeTableInfo['subject_id'], // subject_id qo'shildi
    //                 'subject_category_id' => $timeTableInfo['subject_category_id'], // subject_category_id qo'shildi
    //                 'time_option_id' => $timeTableInfo['time_option_id'], // time_option_id qo'shildi
    //                 'edu_year_id' => $timeTableInfo['edu_year_id'], // edu_year_id qo'shildi
    //                 'edu_semestr_id' => $timeTableInfo['edu_semester_id'], // edu_year_id qo'shildi
    //                 'status' => 2,
    //             ];
    //         }
    //     }

    //     // 8. StudentAttend uchun bulk-insert
    //     if (!empty($newStudentAttendRecords)) {
    //         Yii::$app->db->createCommand()
    //             ->batchInsert(
    //                 StudentAttend::tableName(),
    //                 ['student_id', 'attend_id', 'date', 'time_table_id', 'subject_id', 'subject_category_id', 'time_option_id', 'edu_year_id', 'edu_semestr_id', 'status'],
    //                 $newStudentAttendRecords
    //             )
    //             ->execute();
    //     }

    //     return ExitCode::OK;
    // }


    // public function actionIndex4()
    // {
    //     $today = date('Y-m-d');

    //     // 0. Dars bo'lgan sanalarni olish
    //     $timeTables = TimeTable::find()
    //         ->select('time_table.id, time_table.edu_semester_id, time_table.subject_id')
    //         ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
    //         ->where([
    //             'time_table.is_deleted' => 0,
    //             'time_table.archived' => 0,
    //             'time_table.week_id' => date('N', strtotime($today))
    //         ])
    //         ->andWhere(['<=', 'edu_semestr.start_date', $today])
    //         ->andWhere(['>=', 'edu_semestr.end_date', $today])
    //         ->andWhere(['in', 'time_table.id', [41584]])
    //         ->asArray()
    //         ->all();

    //     if (empty($timeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
    //     $result = $this->isHoliday($today);
    //     if ($result['status'] == 0) {
    //         return ExitCode::OK; // Agar bugun bayram kuni bo'lsa, chiqamiz
    //     }
    //     $today = $result['date'];

    //     $timeTableIds = array_column($timeTables, 'id');

    //     // 2. O'sha dars jadvaliga yozilgan barcha talabalarni bitta so‘rov bilan olish
    //     $studentTimeTables = StudentTimeTable::find()
    //         ->select(['time_table_id', 'student_id'])
    //         ->where(['time_table_id' => $timeTableIds])
    //         ->asArray()
    //         ->all();

    //     if (empty($studentTimeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // Talabalarni guruhlash (time_table_id bo'yicha)
    //     $studentsByTimeTable = [];
    //     foreach ($studentTimeTables as $stt) {
    //         $studentsByTimeTable[$stt['time_table_id']][] = $stt['student_id'];
    //     }

    //     // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish (bulk so‘rov)
    //     $turniketUserIds = Turniket::find()
    //         ->select('user_id')
    //         ->where(['in', 'user_id', Profile::find()->select('user_id')->where(['in', 'user_id', array_column($studentTimeTables, 'student_id')])])
    //         ->andWhere(['date' => $today])
    //         ->indexBy('user_id')
    //         ->asArray()
    //         ->all();

    //     $turniketUserIds = array_keys($turniketUserIds);

    //     // 4. Attend yozuvlarini bulk olish
    //     $attends = Attend::find()
    //         ->where(['date' => $today, 'time_table_id' => $timeTableIds])
    //         ->indexBy('time_table_id')
    //         ->all();

    //     $newStudentAttendRecords = [];

    //     foreach ($studentsByTimeTable as $timeTableId => $studentIds) {
    //         // 5. Kirib kelmagan talabalarni aniqlash
    //         $absentStudents = array_diff($studentIds, $turniketUserIds);

    //         if (empty($absentStudents)) {
    //             continue;
    //         }

    //         // `subject_id` ni olish uchun `TimeTable` ma'lumotlarini topamiz
    //         $timeTableInfo = array_filter($timeTables, fn($tt) => $tt['id'] == $timeTableId);
    //         $timeTableInfo = reset($timeTableInfo); // Birinchi mos yozuvni olish

    //         // 6. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
    //         if (!isset($attends[$timeTableId])) {
    //             $attend = new Attend();
    //             $attend->time_table_id = $timeTableId;
    //             $attend->date = $today;
    //             $attend->student_ids = json_encode(array_values($absentStudents)); // JSON ko'rinishida saqlash
    //             $attend->status = 2; // Yo'qlama
    //             $attend->save();
    //             $attends[$timeTableId] = $attend; // Keyingi ishlash uchun cache'ga saqlaymiz
    //         } else {
    //             $attend = $attends[$timeTableId];
    //             $existingStudentIds = json_decode($attend->student_ids, true) ?? [];
    //             $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
    //             if (!empty($newAbsentStudents)) {
    //                 $attend->student_ids = json_encode(array_merge($existingStudentIds, $newAbsentStudents));
    //                 $attend->save();
    //             }
    //         }

    //         // 7. StudentAttend jadvaliga yozish (bulk insert)
    //         foreach ($absentStudents as $studentId) {
    //             $newStudentAttendRecords[] = [
    //                 'student_id' => $studentId,
    //                 'attend_id' => $attend->id,
    //                 'date' => $today,
    //                 'time_table_id' => $timeTableId,
    //                 'subject_id' => $timeTableInfo['subject_id'], // subject_id qo'shildi
    //                 'status' => 2,
    //             ];
    //         }
    //     }

    //     // 8. StudentAttend uchun bulk-insert
    //     if (!empty($newStudentAttendRecords)) {
    //         Yii::$app->db->createCommand()
    //             ->batchInsert(
    //                 StudentAttend::tableName(),
    //                 ['student_id', 'attend_id', 'date', 'time_table_id', 'subject_id', 'status'],
    //                 $newStudentAttendRecords
    //             )
    //             ->execute();
    //     }

    //     return ExitCode::OK;
    // }
    // public function actionIndex3()
    // {
    //     $today = date('Y-m-d');

    //     // 0. Dars bo'lgan sanalarni olish
    //     $timeTables = TimeTable::find()
    //         ->select('time_table.id, time_table.edu_semester_id')
    //         ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
    //         ->where([
    //             'time_table.is_deleted' => 0,
    //             'time_table.archived' => 0,
    //             'time_table.week_id' => date('N', strtotime($today))
    //         ])
    //         ->andWhere(['<=', 'edu_semestr.start_date', $today])
    //         ->andWhere(['>=', 'edu_semestr.end_date', $today])
    //         ->andWhere(['in', 'time_table.id', [41584]])
    //         ->asArray()
    //         ->all();

    //     if (empty($timeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
    //     $result = $this->isHoliday($today);
    //     if ($result['status'] == 0) {
    //         return ExitCode::OK; // Agar bugun bayram kuni bo'lsa, chiqamiz
    //     }
    //     $today = $result['date'];

    //     $timeTableIds = array_column($timeTables, 'id');

    //     // 2. O'sha dars jadvaliga yozilgan barcha talabalarni bitta so‘rov bilan olish
    //     $studentTimeTables = StudentTimeTable::find()
    //         ->select(['time_table_id', 'student_id'])
    //         ->where(['time_table_id' => $timeTableIds])
    //         ->asArray()
    //         ->all();

    //     if (empty($studentTimeTables)) {
    //         return ExitCode::OK;
    //     }

    //     // Talabalarni guruhlash (time_table_id bo'yicha)
    //     $studentsByTimeTable = [];
    //     foreach ($studentTimeTables as $stt) {
    //         $studentsByTimeTable[$stt['time_table_id']][] = $stt['student_id'];
    //     }

    //     // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish (bulk so‘rov)
    //     $turniketUserIds = Turniket::find()
    //         ->select('user_id')
    //         ->where(['in', 'user_id', Profile::find()->select('user_id')->where(['in', 'user_id', array_column($studentTimeTables, 'student_id')])])
    //         ->andWhere(['date' => $today])
    //         ->indexBy('user_id')
    //         ->asArray()
    //         ->all();

    //     $turniketUserIds = array_keys($turniketUserIds);

    //     // 4. Attend yozuvlarini bulk olish
    //     $attends = Attend::find()
    //         ->where(['date' => $today, 'time_table_id' => $timeTableIds])
    //         ->indexBy('time_table_id')
    //         ->all();

    //     $newStudentAttendRecords = [];

    //     foreach ($studentsByTimeTable as $timeTableId => $studentIds) {
    //         // 5. Kirib kelmagan talabalarni aniqlash
    //         $absentStudents = array_diff($studentIds, $turniketUserIds);

    //         if (empty($absentStudents)) {
    //             continue;
    //         }

    //         // 6. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
    //         if (!isset($attends[$timeTableId])) {
    //             $attend = new Attend();
    //             $attend->time_table_id = $timeTableId;
    //             $attend->date = $today;
    //             $attend->student_ids = json_encode(array_values($absentStudents)); // JSON ko'rinishida saqlash
    //             $attend->status = 2; // Yo'qlama
    //             $attend->save();
    //             $attends[$timeTableId] = $attend; // Keyingi ishlash uchun cache'ga saqlaymiz
    //         } else {
    //             $attend = $attends[$timeTableId];
    //             $existingStudentIds = json_decode($attend->student_ids, true) ?? [];
    //             $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
    //             if (!empty($newAbsentStudents)) {
    //                 $attend->student_ids = json_encode(array_merge($existingStudentIds, $newAbsentStudents));
    //                 $attend->save();
    //             }
    //         }

    //         // 7. StudentAttend jadvaliga yozish (bulk insert)
    //         foreach ($absentStudents as $studentId) {
    //             $newStudentAttendRecords[] = [
    //                 'student_id' => $studentId,
    //                 'attend_id' => $attend->id,
    //                 'date' => $today,
    //                 'time_table_id' => $timeTableId,
    //                 'status' => 2,
    //             ];
    //         }
    //     }

    //     // 8. StudentAttend uchun bulk-insert
    //     if (!empty($newStudentAttendRecords)) {
    //         Yii::$app->db->createCommand()
    //             ->batchInsert(
    //                 StudentAttend::tableName(),
    //                 ['student_id', 'attend_id', 'date', 'time_table_id', 'status'],
    //                 $newStudentAttendRecords
    //             )
    //             ->execute();
    //     }

    //     return ExitCode::OK;
    // }

    // public function actionIndex2manqilgan()
    // {
    //     $today = date('Y-m-d');

    //     // 0. Dars bo'lgan sanalarni olish
    //     $timeTables = TimeTable::find()
    //         ->select('time_table.*')
    //         ->leftJoin('edu_semestr', 'time_table.edu_semester_id = edu_semestr.id')
    //         ->where([
    //             'time_table.is_deleted' => 0,
    //             'time_table.archived' => 0,
    //             'time_table.week_id' => date('N', strtotime($today))
    //         ])
    //         ->andWhere(['<=', 'edu_semestr.start_date', $today])
    //         ->andWhere(['>=', 'edu_semestr.end_date', $today])
    //         ->andWhere(['in', 'time_table.id', [41584]])
    //         ->all();

    //     foreach ($timeTables as $timeTable) {

    //         // 1. Bugun dam olish kuni bo'lmasligini tekshiramiz
    //         $result = $this->isHoliday($today);
    //         if ($result['status'] == 0) {
    //             continue; // Agar bugun bayram kuni bo'lsa, o'tib ketamiz
    //         }
    //         $today = $result['date'];

    //         // 2. O'sha dars jadvaliga yozilgan talabalarni olish
    //         $studentTimeTables = StudentTimeTable::find()
    //             ->where(['time_table_id' => $timeTable->id])
    //             ->all();

    //         $studentIds = array_map(fn($st) => $st->student_id, $studentTimeTables);

    //         if (empty($studentIds)) {
    //             continue;
    //         }

    //         // 3. Ushbu talabalarning turniketdan o'tganligini tekshirish
    //         $turniketUserIds = Turniket::find()
    //             ->select('user_id')
    //             ->where(['in', 'user_id', Profile::find()->select('user_id')->where(['in', 'user_id', $studentIds])])
    //             ->andWhere(['date' => $today])
    //             ->column();



    //         // 4. Kirib kelmagan talabalarni aniqlash
    //         $absentStudents = array_diff($studentIds, $turniketUserIds);

    //         if (!empty($absentStudents)) {
    //             // 5. Attend jadvalini tekshirish (o'qituvchi tomonidan yaratilgan bo'lishi mumkin)
    //             $attend = Attend::find()
    //                 ->where(['time_table_id' => $timeTable->id, 'date' => $today])
    //                 ->one();

    //             if (!$attend) {
    //                 // Agar yo'qlama jadvali mavjud bo'lmasa, yangi yozuv yaratamiz
    //                 $attend = new Attend();
    //                 $attend->time_table_id = $timeTable->id;
    //                 $attend->date = $today;
    //                 $attend->student_ids = ((array)json_decode(json_encode($absentStudents)));
    //                 $attend->status = 2; // Yo'qlama

    //                 $attend->save();
    //             } else {
    //                 // Agar mavjud bo'lsa, `student_ids` ustunini yangilaymiz
    //                 $existingStudentIds = $attend->student_ids;
    //                 $newAbsentStudents = array_diff($absentStudents, $existingStudentIds);
    //                 if (!empty($newAbsentStudents)) {
    //                     $attend->student_ids = ((array)json_decode(json_encode(array_merge($existingStudentIds, $newAbsentStudents))));
    //                     $attend->save();
    //                 }
    //             }

    //             // 6. StudentAttend jadvaliga yozish (faqat yangi talabalar uchun)
    //             foreach ($absentStudents as $studentId) {
    //                 $studentAttend = StudentAttend::findOne([
    //                     'student_id' => $studentId,
    //                     'attend_id' => $attend->id,
    //                     'date' => $today,
    //                     'time_table_id' => $timeTable->id,
    //                 ]);

    //                 if (!$studentAttend) {
    //                     $studentAttend = new StudentAttend();
    //                     $studentAttend->student_id = $studentId;
    //                     $studentAttend->attend_id = $attend->id;
    //                     $studentAttend->date = $today;
    //                     $studentAttend->time_table_id = $timeTable->id;
    //                     $studentAttend->status = 2;
    //                     $studentAttend->save();
    //                 } else {
    //                     $studentAttend->status = 2;
    //                     $studentAttend->save();
    //                 }
    //             }
    //         }
    //     }

    //     return ExitCode::OK;
    // }

    private function isHoliday($date)
    {
        $holiday = Holiday::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['<=', 'start_date', $date])
            ->andWhere(['>=', 'finish_date', $date])
            ->one();
        // print_r(($holiday));
        // die;
        if ($holiday) {
            if ($holiday->type == 2) {
                return [
                    'status' => 1,
                    'date' => $holiday->moved_date
                ];
            }
            return [
                'status' => 0,
            ];
        } else {
            return [
                'status' => 1,
                'date' => $date
            ];
        }
    }
}
