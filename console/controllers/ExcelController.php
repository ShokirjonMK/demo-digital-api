<?php

namespace console\controllers;

use api\resources\SemestrUpdate;
use api\resources\User;
use common\models\model\Area;
use common\models\model\ExamControlStudent;
use common\models\model\ExamTestStudentAnswer;
use common\models\model\FinalExamGroup;
use common\models\model\PasswordEncrypts;
use common\models\model\Pinfl;
use common\models\model\StudentMark;
use common\models\model\StudentMarkHistory;
use common\models\model\StudentMarkVedomst;
use common\models\model\StudentTopicPermission;
use common\models\model\StudentTopicResult;
use Yii;
use base\ResponseStatus;
use common\models\Direction;
use common\models\model\Attend;
use common\models\model\AttendReason;
use common\models\model\EduPlan;
use common\models\model\EduSemestr;
use common\models\model\EduSemestrExamsType;
use common\models\model\EduSemestrSubject;
use common\models\model\EduYear;
use common\models\model\Group;
use common\models\model\LoadRate;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentAttend;
use common\models\model\StudentGroup;
use common\models\model\Subject;
use common\models\model\TeacherAccess;
use common\models\model\TimeTable1;
use common\models\model\UserAccess;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\console\Controller;
use yii\helpers\BaseConsole;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory as WORDIOFactory;

class ExcelController extends Controller
{

    public function actionPin()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $inputFileName = __DIR__ . '/excels/grid-export (22).xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        foreach ($data as $key => $row) {
            if ($key != 0) {
                if ($row[0] == "") {
                    break;
                }
                $q = Pinfl::findOne(['passport_pin' => $row[0]]);
                if (!$q) {
                    $new = new Pinfl();
                    $new->passport_pin = $row[0];
                    $new->save(false);
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            echo "tugadi.";
        }
        $transaction->rollBack();
        dd($errors);
    }

    public function actionFinalSubjectId()
    {
        $query = FinalExamGroup::find()->all();
        foreach ($query as $item) {
            $item->subject_id = $item->eduSemestrSubject->subject_id;
            $item->save(false);
        }
    }

    public function actionStudentDel()
    {
        $stdGroup = StudentGroup::find()->all();
        foreach ($stdGroup as $del) {
            $del->delete();
        }
        $student = Student::find()
            ->where(['edu_form_id' => 2])
            ->all();
        foreach ($student as $std) {
            $us = $std->user_id;
            $std->delete();
            if ($us != null) {
                $profile = Profile::find()->where(['user_id' => $us])->all();
                if (count($profile) > 0) {
                    foreach ($profile as $pr) {
                        $pr->delete();
                    }
                }
                $pas = PasswordEncrypts::find()->where(['user_id' => $us])->all();
                if (count($pas) > 0) {
                    foreach ($pas as $p) {
                        $p->delete();
                    }
                }
                $user = User::findOne($us);
                if ($user) {
                    $user->delete();
                }
            }
        }
    }

    public function actionStudentValidate() {
        $students = Student::find()->all();
        foreach ($students as $student) {
            $group = $student->group;
            if ($student->edu_plan_id != $group->edu_plan_id) {
                echo $student->id."\n";
            }
        }
    }

    public function actionStudentGroup()
    {
        $students = Student::find()->all();
        foreach ($students as $student) {
            $studentGroup = new StudentGroup();
            $studentGroup->student_id = $student->id;
            $studentGroup->group_id = $student->group_id;
            $studentGroup->edu_plan_id = $student->group->edu_plan_id;
            $studentGroup->edu_semestr_id = $student->group->activeEduSemestr->id;
            $studentGroup->edu_year_id = $student->group->activeEduSemestr->edu_year_id;
            $studentGroup->edu_form_id = $student->group->activeEduSemestr->edu_form_id;
            $studentGroup->semestr_id = $student->group->activeEduSemestr->semestr_id;
            $studentGroup->course_id = $student->group->activeEduSemestr->course_id;
            $studentGroup->faculty_id = $student->group->activeEduSemestr->faculty_id;
            $studentGroup->direction_id = $student->group->activeEduSemestr->direction_id;
            $studentGroup->save(false);


            $student->faculty_id = $student->eduPlan->faculty_id;
            $student->direction_id = $student->eduPlan->direction_id;
            $student->course_id = $student->eduPlan->activeSemestr->course_id;
            $student->edu_year_id = $student->eduPlan->activeSemestr->edu_year_id;
            $student->edu_form_id = $student->eduPlan->activeSemestr->edu_form_id;
            $student->update(false);
        }
    }


    public function actionProfileName()
    {
        $profile = Profile::find()->all();
        foreach ($profile as $item) {
            if ($item->first_name != null) {
                $item->first_name = strtoupper($item->first_name);
            }
            if ($item->last_name != null) {
                $item->last_name = strtoupper($item->last_name);
            }
            if ($item->middle_name != null) {
                $item->middle_name = strtoupper($item->middle_name);
            }
            $item->save(false);
        }
    }

    public function actionUserAccessToken()
    {
        $user = User::find()->all();
        foreach ($user as $u) {
            $u->access_token = $u->access_token."1";
            $u->save(false);
        }
    }

    public function actionStdMark()
    {
        $students = StudentMark::find()
            ->where([
                'exam_type_id' => 3,
                'is_deleted' => 0,
                'vedomst' => [0, null]
            ])
            ->all();
        foreach ($students as $student) {
            $student->is_deleted = 4;
            $student->save(false);
        }
    }

    public function actionStudentMarkBug()
    {
        $studentMark = StudentMark::find()->where(['is_deleted' => 0])->all();
        foreach ($studentMark as $item) {
            $query = StudentMark::find()
                ->where([
                    'student_id' => $item->student_id,
                    'edu_semestr_exams_type_id' => $item->edu_semestr_exams_type_id,
                    'edu_semestr_subject_id' => $item->edu_semestr_subject_id,
                    'edu_semestr_id' => $item->edu_semestr_id,
                    'is_deleted' => 0
                ])
                ->orderBy('id desc')
                ->all();
            if (count($query) > 1) {
                $i = 1;
                foreach ($query as $v) {
                    if ($i != 1) {
                        $v->is_deleted = 1;
                        $v->save(false);
                    }
                    $i++;
                }
            }
        }
    }

    public function actionStudentMarkBug2()
    {
        $studentMark = StudentMark::find()->where(['is_deleted' => 0])->all();
        $data = [];
        foreach ($studentMark as $item) {
            $query = StudentMark::find()
                ->where([
                    'student_id' => $item->student_id,
                    'edu_semestr_exams_type_id' => $item->edu_semestr_exams_type_id,
                    'edu_semestr_subject_id' => $item->edu_semestr_subject_id,
                    'edu_semestr_id' => $item->edu_semestr_id,
                    'is_deleted' => 0
                ])
                ->orderBy('id desc')
                ->all();
            if (count($query) > 1) {
                $data[] = $item->id;
            }
        }

        dd($data);

    }

    public function actionStudentMarkBug3()
    {
        $studentMark = StudentMark::find()->where(['is_deleted' => 0])->all();
        $data = [];
        foreach ($studentMark as $item) {
            $student = Student::findOne($item->student_id);
            $group = Group::findOne($item->group_id);
            if ($student->group_id != $group->id) {
                $item->is_deleted = 2;
                $item->save(false);
                $data[] = $item->id;
            }
        }

        dd($data);

    }

    public function actionAttendBugStudent()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $std = [];

        $query = StudentAttend::find()->all();
        foreach ($query as $item) {
            $student = Student::findOne($item->student_id);
            if (!isset($student)) {
                $errors[] = $student->id.'= O\'quvchi mavjud emas.';
            }
            $group = Group::findOne($student->group_id);
            if ($group == null) {
                $errors[] = $student->id.'= O\'quvchini guruhi mavjud emas.';
            }
            $timeTable = TimeTable1::findOne([
                'group_id' => $group->id,
                'subject_id' => $item->subject_id,
                'subject_category_id' => $item->subject_category_id,
//                'status' => 1,
//                'is_deleted' => 0
            ]);
            if ($timeTable == null) {
                $item->delete();
//                $errors[] = ['attend_student' => $item->id, 'student' => $student->id,'category' => $item->subject_category_id];
            } else {
                $item->edu_plan_id = $timeTable->edu_plan_id;
                $item->edu_semestr_id = $timeTable->edu_semestr_id;
                $item->faculty_id = $timeTable->faculty_id;
                $item->course_id = $timeTable->course_id;
                $item->semestr_id = $timeTable->semestr_id;
                $item->save(false);
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            echo "tugadi. \n";
        } else {
            $transaction->rollBack();
            dd($errors);
        }
    }

    public function actionGroupStudents()
    {
        $groups = Group::find()->all();
        foreach ($groups as $group) {
            $students = Student::find()->where(['group_id' => $group->id,'is_deleted' => 0])->count();
            if ($students > 27) {
                echo $group->unical_name."\n";
            }
        }
    }

    public function actionDel() {
        $student = Student::find()
            ->where(['>=' , 'id' , 4810])
            ->all();
        foreach ($student as $std) {
            $profile = Profile::findOne([
                'user_id' => $std->user_id
            ]);
            if (isset($profile)) {
                $profile->delete();
            }
            $studentGroup = StudentGroup::findOne([
                'student_id' => $std->id,
            ]);
            if (isset($studentGroup)) {
                $studentGroup->delete();
            }

            $user = $std->user_id;
            $std->delete();
            $pas = PasswordEncrypts::findOne([
                'user_id' => $user
            ]);
            $pas->delete();
            $user = User::findOne($user);
            $user->delete();
        }
    }

    public function actionUpdateStudentFullName() {
        $query = Profile::find()->all();
        foreach ($query as $profile) {
            if ($profile->first_name != null) {
                $profile->first_name = str_replace('ı', 'i', $profile->first_name);
            }
            if ($profile->last_name != null) {
                $profile->last_name = str_replace('ı', 'i', $profile->last_name);
            }
            if ($profile->middle_name != null) {
                $profile->middle_name = str_replace('ı', 'i', $profile->middle_name);
            }
            $profile->save(false);
        }
    }


    public function actionTimeTable() {
        $timeTable = TimeTable1::find()
            ->where([
                'is_deleted' => 0,
                'two_groups' => 1,
            ])
            ->all();
        foreach ($timeTable as $item) {
            $twoGroups = TimeTable1::find()
                ->where(['is_deleted' => 0, 'two_groups' => 1, 'ids' => $item->ids])
                ->andWhere(['!=' , 'id' , $item->id])
                ->one();
            if ($twoGroups != null) {
                if ($item->id < $twoGroups->id) {
                    $twoGroups->group_type = 2;
                    $twoGroups->save(false);
                }
            }
        }
    }

    public function actionAreaStatus() {
        $query = Area::find()->all();
        foreach ($query as $area) {
            $area->status = 1;
            $area->save(false);
        }
    }

    public function actionSecondGroups() {
        $timeTable = TimeTable1::find()
            ->where([
                'is_deleted' => 0,
                'two_groups' => 1,
                'group_type' => 2,
            ])
            ->all();
        foreach ($timeTable as $item) {
            $item->group_type = 1;
            $item->save(false);
        }
    }

    public function actionEduPlan() {
        $eduPlan = EduPlan::find()
            ->where(['>=', 'id' , 122])
            ->andWhere(['<=' , 'id' , 131])
            ->all();
        foreach ($eduPlan as $item) {
            $eduSemestrs = EduSemestr::find()
                ->where([
                    'edu_plan_id' => $item->id,
                    'is_deleted' => 0
                ])->orderBy('id asc')->all();

            for ($i = 0; $i < count($eduSemestrs); $i += 2) {
                $start = $eduSemestrs[$i]['start_date'];
                $end = $eduSemestrs[$i]['end_date'];
                $eduSemestrs[$i]['start_date'] = $eduSemestrs[$i+1]['start_date'];
                $eduSemestrs[$i]['end_date'] = $eduSemestrs[$i+1]['end_date'];
                $eduSemestrs[$i]->save(false);

                $eduSemestrs[$i+1]['start_date'] = $start;
                $eduSemestrs[$i+1]['end_date'] = $end;
                $eduSemestrs[$i+1]->save(false);
            }
        }
    }


    public function actionWord() {
        // Word faylini o'qish
        $wordFilePath = __DIR__ . '/word/word.docx';
        $phpWord = WORDIOFactory::load($wordFilePath);

        $section = $phpWord->getSection(0);
        $phpWord->getSection(0)->getElement('oldWord');

        // O'zgartirilgan Word faylini saqlash
        $updatedFilePath = __DIR__ . '/word/word1223.docx';
        $phpWordWriter = WORDIOFactory::createWriter($phpWord, 'Word2007');
        $phpWordWriter->save($updatedFilePath);

        // Faylni o'zgartirish muvaffaqiyatli
        return 'Fayl muvaffaqiyatli o\'zgartirildi. Yangi fayl manzili: ' . $updatedFilePath;
    }


    public function actionWord1()
    {
        // Word faylini o'qish
        $wordFilePath = __DIR__ . '/word/word.docx';
        $phpWord = WORDIOFactory::load($wordFilePath);

        // Har bir seksiyadagi matnni tekshirish va o'zgartirish
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                        $element->setText(str_replace('salom', 'ikbol', $element->getText()));
                }
            }
        }

        // O'zgartirilgan Word faylini saqlash
        $updatedFilePath = __DIR__ . '/word/word112.docx';
        $phpWordWriter = WORDIOFactory::createWriter($phpWord, 'Word2007');
        $phpWordWriter->save($updatedFilePath);

        // Faylni o'zgartirish muvaffaqiyatli
        return 'Fayl muvaffaqiyatli o\'zgartirildi. Yangi fayl manzili: ' . $updatedFilePath;
    }


    public function actionAttendDel() {
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $newString1 = str_replace('"', '', $attend->student_ids);
            $attend->student_ids = $newString1;
            $attend->save(false);
        }
    }



    public function actionAttendBug() {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $timeTable = TimeTable1::findOne([
                'id' => $attend->time_table_id,
                'is_deleted' => 0
            ]);
            if (isset($timeTable)) {
                if ($timeTable->two_groups == 1) {
                    if ($timeTable->group_type == 1) {
                        $group_type = 2;
                    } elseif ($timeTable->group_type == 2) {
                        $group_type = 1;
                    }
                    $timeTable2 = TimeTable1::findOne([
                        'ids' => $timeTable->ids,
                        'group_type' => $group_type,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    $atd = Attend::findOne([
                        'time_table_id' => $timeTable2->id,
                        'date' => $attend->date,
                    ]);
                    if ($atd == null) {
                        $new = new Attend();
                        $new->date = $attend->date;
                        $new->time_table_id = $timeTable2->id;
                        $new->student_ids = [];
                        $new->subject_id = $timeTable2->subject_id;
                        $new->subject_category_id = $timeTable2->subject_category_id;
                        $new->edu_year_id = $timeTable2->edu_year_id;
                        $new->edu_semestr_id = $timeTable2->edu_semestr_id;
                        $new->faculty_id = $timeTable2->faculty_id;
                        $new->edu_plan_id = $timeTable2->edu_plan_id;
                        $new->semestr_id = $timeTable2->semestr_id;
                        $new->type = $timeTable2->eduSemestr->semestr->type;
                        $new->group_id = $timeTable2->group_id;
                        $new->save(false);
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function actionAttendBug2() {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $timeTable = TimeTable1::findOne([
                'id' => $attend->time_table_id,
                'is_deleted' => 0
            ]);
            if (isset($timeTable)) {
                if ($timeTable->two_groups == 1) {
                    if ($timeTable->group_type == 1) {
                        $group_type = 2;
                    } elseif ($timeTable->group_type == 2) {
                        $group_type = 1;
                    }
                    $timeTable2 = TimeTable1::findOne([
                        'ids' => $timeTable->ids,
                        'group_type' => $group_type,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    $atd = Attend::findOne([
                        'time_table_id' => $timeTable2->id,
                        'date' => $attend->date,
                    ]);

                    $array1 = $attend->student_ids;
                    $array2 = $atd->student_ids;
                    $mergedArray = array_merge($array1, $array2);
                    $uniqueArray = array_unique($mergedArray);

                    $attend->student_ids = $uniqueArray;
                    $attend->save(false);
                    $atd->student_ids = $uniqueArray;
                    $atd->save(false);
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function actionAttendBug3() {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $timeTable = TimeTable1::findOne([
                'id' => $attend->time_table_id,
                'is_deleted' => 0
            ]);
            if (isset($timeTable)) {
                if ($timeTable->two_groups == 1) {

                    $array1 = $attend->student_ids;
                    if (count($array1) > 0) {
                        $d1 = [];
                        foreach ($array1 as $v1) {
                            $stu = Student::findOne($v1);
                            if ($stu->type != $timeTable->group_type) {
                                $d1[] = $v1;
                            }
                        }
                        $ar = $array1;
                        $arr = array_merge(array_diff($ar, $d1));
                        $attend->student_ids = $arr;
                        $attend->save(false);
                    }

                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function actionAttendBug4() {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $timeTable = TimeTable1::findOne([
                'id' => $attend->time_table_id,
                'is_deleted' => 0
            ]);
            if (isset($timeTable)) {
                if ($timeTable->two_groups == 1) {
                    if ($timeTable->group_type == 1) {
                        $group_type = 2;
                    } elseif ($timeTable->group_type == 2) {
                        $group_type = 1;
                    }
                    $timeTable2 = TimeTable1::findOne([
                        'ids' => $timeTable->ids,
                        'group_type' => $group_type,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    $atd = Attend::findOne([
                        'time_table_id' => $timeTable2->id,
                        'date' => $attend->date,
                    ]);
                    $st1 = $attend->student_ids;
                    if (count($st1) > 0) {
                        foreach ($st1 as $v1) {
                            $attStudent = StudentAttend::findOne([
                                'time_table_id' => $timeTable->id,
                                'attend_id' => $attend->id,
                                'student_id' => $v1
                            ]);
                            if ($attStudent == null) {
                                $attStudent = StudentAttend::findOne([
                                    'time_table_id' => $timeTable2->id,
                                    'attend_id' => $atd->id,
                                    'student_id' => $v1
                                ]);
                                if ($attStudent != null) {
                                    $attStudent->time_table_id = $timeTable->id;
                                    $attStudent->attend_id = $attend->id;
                                    $attStudent->save(false);
                                } else {
                                    dd(2222);
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }





    public function actionAttendStudent() {
        $query = StudentAttend::find()
            ->where(['is_deleted' => 1])
            ->all();
        foreach ($query as $std) {
            $std->is_deleted = 0;
            $std->save(false);
        }
    }



    public function actionAttend() {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere(['!=', 'subject_category_id', 1])
            ->all();

        foreach ($attends as $attend) {
            $group_id = $attend->timeTable->group_id;
            $ids = $attend->timeTable->ids;
            $students = $attend->student_ids;
            $data = [];
            foreach ($students as $student) {
                $sts = Student::findOne($student)->group_id;
                if ($sts != $group_id) {
                    $data[] = [$sts => $student];
                }
            }
            if (count($data) > 0) {
                foreach ($data as  $vs) {
                    foreach ($vs as $key => $v) {
                        $timeTable = TimeTable1::findOne([
                            'ids' => $ids,
                            'group_id' => $key,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        $query = Attend::findOne([
                            'time_table_id' => $timeTable->id,
                            'group_id' => $key,
                            'date' => $attend->date,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($query != null) {
                            $qq = $query->student_ids;
                            array_push($qq, $v);
                            $query->student_ids = $qq;
                            if ($query->save(false)) {
                                $stu = StudentAttend::findOne([
                                    'attend_id' => $attend->id,
                                    'student_id' => $v,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ]);
                                if ($stu != null) {
                                    $stu->attend_id = $query->id;
                                    $stu->time_table_id = $query->time_table_id;
                                    $stu->subject_id = $query->subject_id;
                                    $stu->subject_category_id = $query->subject_category_id;
                                    $stu->edu_year_id = $query->edu_year_id;
                                    $stu->edu_semestr_id = $query->edu_semestr_id;
                                    $stu->faculty_id = $query->faculty_id;
                                    $stu->course_id = $query->timeTable->course_id;
                                    $stu->edu_plan_id = $query->edu_plan_id;
                                    $stu->semestr_id = $query->eduSemestr->semestr_id;
                                    $stu->type = $query->type;
                                    $stu->save(false);
                                } else {
                                    $errors[] = "Talaba yo'q";
                                }
                            }
                        } else {
                            $attendNew = new Attend();
                            $attendNew->time_table_id = $timeTable->id;
                            $attendNew->date = $attend->date;
                            $attendNew->student_ids = array($v);
                            $attendNew->subject_id = $attendNew->timeTable->subject_id;
                            $attendNew->subject_category_id = $attendNew->timeTable->subject_category_id;
                            $attendNew->edu_year_id = $attendNew->timeTable->edu_year_id;
                            $attendNew->edu_semestr_id = $attendNew->timeTable->edu_semestr_id;
                            $attendNew->faculty_id = $attendNew->timeTable->eduPlan->faculty_id;
                            $attendNew->edu_plan_id = $attendNew->timeTable->edu_plan_id;
                            $attendNew->type = $attendNew->eduSemestr->semestr->type;
                            $attendNew->semestr_id = $attendNew->eduSemestr->semestr_id;
                            $attendNew->group_id = $key;

                            if (!($attendNew->validate())) {
                                $errors[] = $attendNew->errors;
                                $transaction->rollBack();
                                return simplify_errors($errors);
                            }
                            if ($attendNew->save(false)) {
                                $stu = StudentAttend::findOne([
                                    'attend_id' => $attend->id,
                                    'student_id' => $v,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ]);
                                if ($stu != null) {
                                    $stu->attend_id = $attendNew->id;
                                    $stu->time_table_id = $attendNew->time_table_id;
                                    $stu->subject_id = $attendNew->subject_id;
                                    $stu->subject_category_id = $attendNew->subject_category_id;
                                    $stu->edu_year_id = $attendNew->edu_year_id;
                                    $stu->edu_semestr_id = $attendNew->edu_semestr_id;
                                    $stu->faculty_id = $attendNew->faculty_id;
                                    $stu->course_id = $attendNew->timeTable->course_id;
                                    $stu->edu_plan_id = $attendNew->edu_plan_id;
                                    $stu->semestr_id = $attendNew->eduSemestr->semestr_id;
                                    $stu->type = $attendNew->type;
                                    $stu->save(false);
                                } else {
                                    $errors[] = "Talaba yo'q";
                                }
                            }
                        }
                    }
                }
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public function actionAttendStudentDel() {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $attends = Attend::find()
            ->where([
                'subject_category_id' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();
        foreach ($attends as $attend) {
            $group_id = $attend->timeTable->group_id;
            $students = $attend->student_ids;
            $data = [];
            foreach ($students as $key => $student) {
                $sts = Student::findOne($student)->group_id;
                if ($sts != $group_id) {
                    $data[] = [$sts => $student];
                    $attend->student_ids = array_diff($attend->student_ids, [$student]);
                    $attend->save(false);
                }
            }
        }
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public function actionTest()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        $inputFileName = __DIR__ . '/excels/sirtqi.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        foreach ($data as $key => $row) {
            if ($key != 0) {

                $name = ( $row[0] );
                $fac = ( $row[1] );
                $direc = ( $row[2] );
                $plan = ( $row[3] );
                $lang = ( $row[4] );

                if ($name == 123) {
                    break;
                }

                $group = new Group();
                $group->faculty_id = $fac;
                $group->direction_id = $direc;
                $group->unical_name = $name;
                $group->edu_plan_id = $plan;
                $group->language_id = $lang;
                if (!$group->validate()) {
                    $errors[] = $group->errors;
                } else {
                    $group->save(false);
                }
            }
        }


        if (count($errors) == 0) {
            $transaction->commit();
            echo "tugadi.";
        }
        $transaction->rollBack();
        dd($errors);
    }

    public function actionSubjectBall() {
        $subjects = Subject::find()->all();
        foreach ($subjects as $subject) {
            $ball = [1 => 30, 2 => 30 , 3 => 30 , 5 => 10];
            $subject->edu_semestr_exams_types = json_encode($ball);
            $subject->save(false);
        }
    }

    public function actionSubjectMaxBall() {
        $subjects = Subject::find()->all();
        foreach ($subjects as $subject) {
            $subject->max_ball = 100;
            $subject->save(false);
        }
    }

    public function actionEduSemestrSubjectBall() {
        $subjects = EduSemestrSubject::find()->all();
        foreach ($subjects as $subject) {
            $oraliq = EduSemestrExamsType::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'exams_type_id' => 1,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($oraliq != null) {
                $oraliq->max_ball = 30;
                $oraliq->save(false);
            } else {
                $newOraliq = new EduSemestrExamsType();
                $newOraliq->edu_semestr_subject_id = $subject->id;
                $newOraliq->exams_type_id = 1;
                $newOraliq->max_ball = 30;
                $newOraliq->save(false);
            }

            $joriy = EduSemestrExamsType::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'exams_type_id' => 2,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($joriy != null) {
                $joriy->max_ball = 30;
                $joriy->save(false);
            } else {
                $newJoriy = new EduSemestrExamsType();
                $newJoriy->edu_semestr_subject_id = $subject->id;
                $newJoriy->exams_type_id = 2;
                $newJoriy->max_ball = 30;
                $newJoriy->save(false);
            }

            $yakuniy = EduSemestrExamsType::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'exams_type_id' => 3,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($yakuniy != null) {
                $yakuniy->max_ball = 30;
                $yakuniy->save(false);
            } else {
                $newJYakuniy = new EduSemestrExamsType();
                $newJYakuniy->edu_semestr_subject_id = $subject->id;
                $newJYakuniy->exams_type_id = 3;
                $newJYakuniy->max_ball = 30;
                $newJYakuniy->save(false);
            }

            $mustaqil = EduSemestrExamsType::findOne([
                'edu_semestr_subject_id' => $subject->id,
                'exams_type_id' => 5,
                'status' => 1,
                'is_deleted' => 0
            ]);
            if ($mustaqil != null) {
                $mustaqil->max_ball = 10;
                $mustaqil->save(false);
            } else {
                $newMustaqil = new EduSemestrExamsType();
                $newMustaqil->edu_semestr_subject_id = $subject->id;
                $newMustaqil->exams_type_id = 5;
                $newMustaqil->max_ball = 10;
                $newMustaqil->save(false);
            }

            $query = EduSemestrExamsType::find()->where([
                'edu_semestr_subject_id' => $subject->id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
            $sum = 0;
            foreach ($query as $item) {
                $sum = $sum + $item->max_ball;
            }
            $subject->max_ball = $sum;
            $subject->save(false);
            if ($sum != 100) {
                echo $subject->id."\n";
            }
        }
        echo "Tugadi \n";
    }

    public function actionUserStatus() {
        $users = User::find()->where(['status' => null])->all();
        foreach ($users as $user) {
            $user->status = 10;
            $user->save(false);
        }
    }

    public function actionAaa() {

        $user = User::findAll([
            'deleted' => 1
        ]);
        if (count($user) > 0) {
            foreach ($user as $item) {

                $userAccess = UserAccess::findAll(['user_id' => $item->id]);
                if (count($userAccess) > 0) {
                    foreach ($userAccess as $userAccessOne) {
                        $userAccessOne->is_deleted = 1;
                        $userAccessOne->save(false);
                    }
                }
                $teacherAccess = TeacherAccess::findAll(['user_id' => $item->id]);
                if (count($teacherAccess) > 0) {
                    foreach ($teacherAccess as $teacherAccessOne) {
                        $teacherAccessOne->is_deleted = 1;
                        $teacherAccessOne->save(false);
                    }
                }

            }
        }

    }

    public function actionGroupName() {
        $groups = Group::find()->all();
        foreach ($groups as $group) {
            $char = $group->unical_name;
            $name = str_replace(' ', '', $char);
            $group->unical_name = $name;
            $group->save(false);
        }
    }

    public function actionCourse() {
        $students = Student::find()->all();

        foreach ($students as $student) {
            if ($student->group_id != null) {
                $group = Group::findOne($student->group_id);
                if ($group != null) {
                    $student->faculty_id = $group->faculty_id;
                    $student->direction_id = $group->direction_id;
                    $student->edu_plan_id = $group->edu_plan_id;
                    $student->course_id = $group->activeEduSemestr->course_id;
                    $student->save(false);
                    $studentGroup = StudentGroup::findOne(['student_id' => $student->id]);
                    if ($studentGroup != null) {
                        $eduSemestrOne = EduSemestr::find()
                            ->where([
                                'edu_plan_id' => $student->edu_plan_id,
                            ])
                            ->orderBy([
                                'course_id' => SORT_ASC,
                                'semestr_id' => SORT_ASC
                            ])
                            ->one();
                        $eduSemestrTwo = EduSemestr::find()
                            ->where([
                                'edu_plan_id' => $student->edu_plan_id,
                            ])
                            ->orderBy([
                                'course_id' => SORT_DESC,
                                'semestr_id' => SORT_DESC
                            ])
                            ->one();
                        $studentGroup->start_date = date("Y-m-d" , strtotime($eduSemestrOne->start_date));
                        $studentGroup->end_date = date("Y-m-d" , strtotime($eduSemestrTwo->end_date));
                        $studentGroup->save(false);
                    }
                }
            }
        }

    }


    public function actionStudent()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        $errors = [];

        $inputFileName = __DIR__ . '/excels/one4.xlsx';
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $q = 1;
        foreach ($data as $key => $row) {

            if ($key != 0) {
                $excelId = $row[0];
                $first_name = $row[2];
                $last_name = $row[1];
                $middle_name = $row[3];
                $fuqarolik = $row[4];
                $millat = $row[5];
                $viloyat = $row[6];
                $jins = $row[7];
                $t_sana = $row[8];
                $p_seriya = $row[9];
                $p_raqam = $row[10];
                $jshr = $row[11];
                $p_b = $row[12];
                $guruh = $row[13];
                $t_sh = 1;

                if ($excelId == null) {
                    break;
                }

                $group = Group::findOne(['unical_name' => $guruh]);

                $role = 'student';
                if (isset($group)) {

                    $eduPlan = EduPlan::findOne($group->edu_plan_id);
                    $activeSemestr = $eduPlan->activeSemestr;

                    if ($activeSemestr == null) {
                        $errors[] = $eduPlan->id." Active Semestr Not found!";
                        break;
                    }
                    $eduSemestrSubject = $activeSemestr->eduSemestrSubjects;

                    // user yaratadi
                    $model = new User();
                    $user = self::studentLogin();
                    $model->username = $user['username'];
                    $model->email= $user['email'];
                    $password = _passwordMK();
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                    $model->auth_key = \Yii::$app->security->generateRandomString(20);
                    $model->password_reset_token = null;
                    $model->access_token = \Yii::$app->security->generateRandomString();
                    $model->access_token_time = time();
                    if (!$model->save(false)) {
                        $errors[] = $excelId." - student model"; break;
                    }

                    $model->savePassword($password, $model->id);

                    $profile = new Profile();
                    $profile->user_id = $model->id;
                    $profile->first_name = $first_name;
                    $profile->last_name = $last_name;
                    $profile->middle_name = $middle_name;
                    $profile->passport_serial = $p_seriya;
                    $profile->passport_number = $p_raqam;
                    $profile->passport_pin = $jshr;
                    $profile->passport_given_date = $p_b;
                    $profile->gender = $jins;
                    $profile->birthday = $t_sana;
                    $profile->permanent_countries_id = 229;
                    $profile->description = $excelId;
                    $profile->nationality_id = $millat;
                    $profile->citizenship_id = $fuqarolik;
                    $profile->region_id = $viloyat;

                    if (!$profile->save(false)) {
                        $errors[] = $excelId." - student profile"; break;
                    }

                    $student = new Student();
                    $student->group_id = $group->id;
                    $student->user_id = $model->id;
                    $student->faculty_id = $group->faculty_id;
                    $student->direction_id = $group->direction_id;
                    $student->edu_plan_id = $group->edu_plan_id;
                    $student->edu_lang_id = $group->language_id;

                    $student->edu_type_id = $eduPlan->edu_type_id;
                    $student->edu_form_id = $eduPlan->edu_form_id;
                    $student->edu_year_id = $activeSemestr->edu_year_id;
                    $student->course_id = $activeSemestr->course_id;
                    $student->gender = $jins;
                    $student->is_contract = $t_sh;
                    $student->type = 1;
                    $student->status = 10;

                    if (!$student->save(false)) {
                        $errors[] =  $excelId." - student save"; break;
                    }

                    $auth = \Yii::$app->authManager;
                    $authorRole = $auth->getRole($role);
                    $auth->assign($authorRole, $model->id);


                    $newStudentGroup = new StudentGroup();
                    $newStudentGroup->student_id = $student->id;
                    $newStudentGroup->group_id = $group->id;
                    $newStudentGroup->edu_year_id = $activeSemestr->edu_year_id;
                    $newStudentGroup->edu_plan_id = $activeSemestr->edu_plan_id;
                    $newStudentGroup->edu_semestr_id = $activeSemestr->id;
                    $newStudentGroup->edu_form_id = $activeSemestr->edu_form_id;
                    $newStudentGroup->semestr_id = $activeSemestr->semestr_id;
                    $newStudentGroup->course_id = $activeSemestr->course_id;
                    $newStudentGroup->faculty_id = $activeSemestr->faculty_id;
                    $newStudentGroup->direction_id = $activeSemestr->direction_id;

                    if (!$newStudentGroup->save(false)) {
                        $errors[] = $excelId . " Student Group validate errors!";
                        break;
                    } else {
                        $result = SemestrUpdate::new($newStudentGroup , $eduSemestrSubject);
                        if (!$result['is_ok']) {
                            $errors[] = $excelId . " Student Subject create errors!";
                            break;
                        }
                    }

                } else {
                    $errors[] = $guruh;
                }
            }

            echo $q."\n";
            $q++;
        }


        if (count($errors) == 0) {
            $transaction->commit();
            echo "Success";
        } else {
            foreach ($errors as $error) {
                echo $error."\n";
            }
        }
    }

    public function actionStudentStatus()
    {
        $student = Student::find()->where(['status' => 1])->all();
        foreach ($student as $item) {
            $item->status = 10;
            $item->save(false);
        }
        echo "tugadi \n";
    }

    public static function studentLogin() {
        $result = [];
        $std = User::find()->orderBy(['id' => SORT_DESC])->one();
        if ($std) {
            $count = $std->id + 10000 + 1;
        } else {
            $count = 10000 + 1;
        }

        $result['username'] = 'sarbon-std-' . $count;
        if (!(isset($post['email']))) {
            $result['email'] = 'sarbon-std' . $count . '@sarbon.uz';
        }
        return $result;
    }


    public function actionStudentUpdate() {
        $groups = Group::find()->all();
        foreach ($groups as $group) {
            $students = Student::find()->where(['group_id' => $group->id])->all();
            if (isset($students)) {
                $eduPlan = EduPlan::findOne($group->edu_plan_id);
                foreach ($students as $student) {
                    $student->faculty_id = $group->faculty_id;
                    $student->direction_id = $group->direction_id;
                    $student->edu_year_id = $eduPlan->edu_year_id;
                    $student->edu_type_id = $eduPlan->edu_type_id;
                    $student->edu_form_id = $eduPlan->edu_form_id;
                    $student->edu_plan_id = $eduPlan->id;
                    $student->save(false);
                }
            }
        }
        echo "tugadi \n";
    }

    public function actionEduSemestrSubject() {
        $query = EduSemestrSubject::find()->all();
        foreach ($query as $item) {
            $subject = Subject::findOne($item->subject_id);
            if (isset($subject)) {
                $item->subject_type_id = $subject->subject_type_id;
                $item->save(false);
            }
        }
        echo "tugadi :) \n";
    }

    public function actionCheck(){
        $eduPlans = EduPlan::find()->all();
        foreach ($eduPlans as $eduPlan) {
            $eduSemestrs = EduSemestr::find()
                ->where([
                    'edu_plan_id' => $eduPlan->id,
                ])
                ->orderBy('id asc')
                ->all();

            $year = EduYear::findOne(['id' => $eduPlan->edu_year_id]);

            if (isset($eduSemestrs)) {
                $i = 0;
                $a = 1;
                foreach ($eduSemestrs as $eduSemestr) {
                    $queryYear = EduYear::findOne([
                        'start_year' => $year->start_year + $i,
                        'type' => $eduSemestr->type
                    ]);
                    if ($a%2 == 0) {
                        $i++;
                    }
                    $a++;
                    $eduSemestr->edu_year_id = $queryYear->id;
                    $eduSemestr->edu_type_id = $eduPlan->edu_type_id;
                    $eduSemestr->edu_form_id = $eduPlan->edu_form_id;
                    $eduSemestr->faculty_id = $eduPlan->faculty_id;
                    $eduSemestr->direction_id = $eduPlan->direction_id;
                    $eduSemestr->save(false);
                }
            }
        }

        echo "tugadi \n";


//        $eduSemestrs = EduSemestr::find()->all();
//        foreach ($eduSemestrs as  $eduSemestr){
//            $eduPlanId = $eduSemestr->edu_plan_id;
//            $edyPlanOne = EduPlan::findOne($eduPlanId);
//            if($edyPlanOne->faculty_id != $eduSemestr->faculty_id || $edyPlanOne->direction_id != $eduSemestr->direction_id){
//                var_dump($eduPlanId."\n");
//            }
//        }

    }
}
