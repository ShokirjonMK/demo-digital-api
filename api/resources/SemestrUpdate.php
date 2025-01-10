<?php

namespace api\resources;

use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubjectCategoryTime;
use common\models\model\Group;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentGroup as CommonStudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use common\models\model\TimetableDate;
use common\models\SubjectInfo;
use Yii;

class SemestrUpdate extends CommonStudentGroup
{
    use ResourceTrait;

    public static function typeOne($post)
    {
        $errors = [];

        $oldEduSemestrId = $post['old_edu_semestr_id'];
        $oldGroupId = $post['old_group_id'];
        $students = array_unique(json_decode($post['students']));
        $studentCount = count($students);

        if ($studentCount == 0) {
            $errors[] = _e('Student not found.');
            return ['is_ok' => false , 'errors' => $errors];
        }

        $oldEduSemestr = EduSemestr::findOne($oldEduSemestrId);
        $newEduSemestr = EduSemestr::findOne([
            'edu_plan_id' => $oldEduSemestr->edu_plan_id,
            'semestr_id' => $oldEduSemestr->semestr_id + 1,
            'is_deleted' => 0
        ]);

        $eduSemestrSubject = $newEduSemestr->eduSemestrSubjects;

        for ($i = 0; $i < $studentCount; $i++) {
            $studentGroups = StudentGroup::findOne([
                'edu_semestr_id' => $oldEduSemestrId,
                'group_id' => $oldGroupId,
                'student_id' => $students[$i],
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$studentGroups) {
                $errors[] = _e('Errors!');
                return ['is_ok' => false , 'errors' => $errors];
            }
            $isStudentGroup = StudentGroup::findOne([
                'edu_plan_id' => $newEduSemestr->edu_plan_id,
                'edu_semestr_id' => $newEduSemestr->id,
                'student_id' => $students[$i],
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$isStudentGroup) {
                $newStudentGroup = new StudentGroup();
                $newStudentGroup->student_id = $students[$i];
                $newStudentGroup->group_id = $oldGroupId;
                $newStudentGroup->edu_year_id = $newEduSemestr->edu_year_id;
                $newStudentGroup->edu_plan_id = $newEduSemestr->edu_plan_id;
                $newStudentGroup->edu_semestr_id = $newEduSemestr->id;
                $newStudentGroup->edu_form_id = $newEduSemestr->edu_form_id;
                $newStudentGroup->semestr_id = $newEduSemestr->semestr_id;
                $newStudentGroup->course_id = $newEduSemestr->course_id;
                $newStudentGroup->faculty_id = $newEduSemestr->faculty_id;
                $newStudentGroup->direction_id = $newEduSemestr->direction_id;
                if (!$newStudentGroup->validate()) {
                    $errors[] = $newStudentGroup->errors;
                    return ['is_ok' => false , 'errors' => $errors];
                } else {
                    $newStudentGroup->save(false);
                    $student = Student::findOne($newStudentGroup->student_id);
                    $student->group_id = $oldGroupId;
                    $student->faculty_id = $newStudentGroup->faculty_id;
                    $student->direction_id = $newStudentGroup->direction_id;
                    $student->course_id = $newStudentGroup->course_id;
                    $student->edu_year_id = $newStudentGroup->edu_year_id;
                    $student->edu_type_id = $newEduSemestr->edu_type_id;
                    $student->edu_plan_id = $newStudentGroup->edu_plan_id;
                    $student->save(false);
//                    $result = self::new($newStudentGroup , $eduSemestrSubject);
//                    if (!$result['is_ok']) {
//                        return ['is_ok' => false , 'errors' => $result['errors']];
//                    }
                }
            }
        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function typeTwo($post)
    {
        $errors = [];

        $oldEduSemestrId = $post['old_edu_semestr_id'];
        $groupId = $post['group_id'];
        $eduPlanId = $post['edu_plan_id'];

        $students = array_unique(json_decode($post['students']));
        $studentCount = count($students);

        if ($studentCount == 0) {
            $errors[] = _e('Student not found.');
            return ['is_ok' => false , 'errors' => $errors];
        }

        $isGroup = Group::findOne([
            'id' => $groupId,
            'edu_plan_id' => $eduPlanId,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$isGroup) {
            $errors[] = _e('Group not found.');
            return ['is_ok' => false , 'errors' => $errors];
        }

        $oldEduSemestr = EduSemestr::findOne($oldEduSemestrId);
        $newEduSemestr = EduSemestr::findOne([
            'edu_plan_id' => $eduPlanId,
            'semestr_id' => $oldEduSemestr->semestr_id + 1,
            'is_deleted' => 0
        ]);
        if ($newEduSemestr->status == 1) {
            $errors[] = _e('Edu Semestr status active.');
            return ['is_ok' => false , 'errors' => $errors];
        }

        $eduSemestrSubject = $newEduSemestr->eduSemestrSubjects;

        for ($i = 0; $i < $studentCount; $i++) {
            $studentGroups = StudentGroup::findOne([
                'edu_semestr_id' => $oldEduSemestrId,
                'student_id' => $students[$i],
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$studentGroups) {
                $errors[] = _e('Errors!');
                return ['is_ok' => false , 'errors' => $errors];
            }
            $isStudentGroup = StudentGroup::findOne([
                'edu_plan_id' => $newEduSemestr->edu_plan_id,
                'edu_semestr_id' => $newEduSemestr->id,
                'student_id' => $students[$i],
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$isStudentGroup) {
                $newStudentGroup = new StudentGroup();
                $newStudentGroup->student_id = $students[$i];
                $newStudentGroup->group_id = $groupId;
                $newStudentGroup->edu_year_id = $newEduSemestr->edu_year_id;
                $newStudentGroup->edu_plan_id = $newEduSemestr->edu_plan_id;
                $newStudentGroup->edu_semestr_id = $newEduSemestr->id;
                $newStudentGroup->edu_form_id = $newEduSemestr->edu_form_id;
                $newStudentGroup->semestr_id = $newEduSemestr->semestr_id;
                $newStudentGroup->course_id = $newEduSemestr->course_id;
                $newStudentGroup->faculty_id = $newEduSemestr->faculty_id;
                $newStudentGroup->direction_id = $newEduSemestr->direction_id;
                if (!$newStudentGroup->validate()) {
                    $errors[] = $newStudentGroup->errors;
                    return ['is_ok' => false , 'errors' => $errors];
                } else {
                    $newStudentGroup->save(false);
                    $student = Student::findOne($newStudentGroup->student_id);
                    $student->group_id = $groupId;
                    $student->faculty_id = $newStudentGroup->faculty_id;
                    $student->direction_id = $newStudentGroup->direction_id;
                    $student->course_id = $newStudentGroup->course_id;
                    $student->edu_year_id = $newStudentGroup->edu_year_id;
                    $student->edu_type_id = $newEduSemestr->edu_type_id;
                    $student->edu_plan_id = $newStudentGroup->edu_plan_id;
                    $student->save(false);
//                    $result = self::new($newStudentGroup , $eduSemestrSubject);
//                    if (!$result['is_ok']) {
//                        return ['is_ok' => false , 'errors' => $result['errors']];
//                    }
                }
            }
        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function new($model, $eduSemestrSubject)
    {
        $errors = [];

        foreach ($eduSemestrSubject as $subject) {
            $eduSemestrSubjectExamTypes = $subject->eduSemestrExamsTypes;
            $studentSemestrSubject = new StudentSemestrSubject();
            $studentSemestrSubject->edu_plan_id = $model->edu_plan_id;
            $studentSemestrSubject->edu_semestr_id = $model->edu_semestr_id;
            $studentSemestrSubject->edu_semestr_subject_id = $subject->id;
            $studentSemestrSubject->student_id = $model->student_id;
            $studentSemestrSubject->student_user_id = $model->student->user_id;
            $studentSemestrSubject->faculty_id = $model->faculty_id;
            $studentSemestrSubject->direction_id = $model->direction_id;
            $studentSemestrSubject->edu_form_id = $model->edu_form_id;
            $studentSemestrSubject->edu_year_id = $model->edu_year_id;
            $studentSemestrSubject->course_id = $model->eduSemestr->course_id;
            $studentSemestrSubject->semestr_id = $model->semestr_id;
            if (!$studentSemestrSubject->validate()) {
                $errors[] = $studentSemestrSubject->errors;
                return ['is_ok' => false , 'errors' => $errors];
            } else {
                $studentSemestrSubject->save(false);
                $studentVedomst = new StudentSemestrSubjectVedomst();
                $studentVedomst->student_semestr_subject_id = $studentSemestrSubject->id;
                $studentVedomst->subject_id = $subject->subject_id;
                $studentVedomst->edu_year_id = $model->edu_year_id;
                $studentVedomst->semestr_id = $model->semestr_id;
                $studentVedomst->student_id = $model->student_id;
                $studentVedomst->student_user_id = $studentSemestrSubject->student_user_id;
                $studentVedomst->group_id = $model->group_id;
                $studentVedomst->vedomst = 1;
                if (!$studentVedomst->validate()) {
                    $errors[] = $studentVedomst->errors;
                    return ['is_ok' => false , 'errors' => $errors];
                } else {
                    $studentVedomst->save(false);
                    foreach ($eduSemestrSubjectExamTypes as $eduSemestrSubjectExamType) {
                        $studentMark = new StudentMark();
                        $studentMark->edu_semestr_exams_type_id = $eduSemestrSubjectExamType->id;
                        $studentMark->exam_type_id = $eduSemestrSubjectExamType->exams_type_id;
                        $studentMark->group_id = $model->group_id;
                        $studentMark->student_id = $model->student_id;
                        $studentMark->student_user_id = $studentVedomst->student_user_id;
                        $studentMark->max_ball = $eduSemestrSubjectExamType->max_ball;
                        $studentMark->edu_semestr_subject_id = $subject->id;
                        $studentMark->subject_id = $subject->subject_id;
                        $studentMark->edu_plan_id = $model->edu_plan_id;
                        $studentMark->edu_semestr_id = $model->edu_semestr_id;
                        $studentMark->faculty_id = $model->faculty_id;
                        $studentMark->direction_id = $model->direction_id;
                        $studentMark->semestr_id = $model->semestr_id;
                        $studentMark->course_id = $studentSemestrSubject->course_id;
                        $studentMark->vedomst = 1;
                        $studentMark->student_semestr_subject_vedomst_id = $studentVedomst->id;
                        if (!$studentMark->validate()) {
                            $errors[] = $studentMark->errors;
                            return ['is_ok' => false , 'errors' => $errors];
                        } else {
                            $studentMark->save(false);
                        }
                    }
                }
            }
        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function deleteItem($studentGroup)
    {
        $errors = [];

        $query = StudentGroup::find()
            ->where([
                'student_id' => $studentGroup->student_id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->orderBy([
                // 'id' => SORT_DESC,
                'semestr_id' => SORT_DESC,
                // 'course_id' => SORT_DESC,
            ])->one();

        if ($studentGroup->id != $query->id) {
            $errors[] = _e('Errors.');
        } else {

            StudentSemestrSubject::updateAll(['is_deleted' => 1] , ['student_id' => $query->student_id, 'edu_semestr_id' => $query->edu_semestr_id, 'is_deleted' => 0]);
            StudentSemestrSubjectVedomst::updateAll(['is_deleted' => 1] , ['student_id' => $query->student_id, 'edu_year_id' => $query->edu_year_id, 'is_deleted' => 0]);
            StudentMark::updateAll(['is_deleted' => 1] , ['student_id' => $query->student_id, 'edu_semestr_id' => $query->edu_semestr_id, 'is_deleted' => 0]);

            $query->is_deleted = 1;
            $query->update(false);

            $oldQuery = StudentGroup::find()
                ->where([
                    'student_id' => $studentGroup->student_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->orderBy([
                    // 'id' => SORT_DESC,
                    'semestr_id' => SORT_DESC,
                    // 'course_id' => SORT_DESC,
                ])->one();

            $student = Student::findOne($studentGroup->student_id);
            $student->group_id = null;
            $student->faculty_id = null;
            $student->direction_id = null;
            $student->course_id = null;
            $student->edu_year_id = null;
            $student->edu_type_id = null;
            $student->edu_form_id = null;
            $student->edu_plan_id = null;
            if ($oldQuery) {
                $student->group_id = $oldQuery->group_id;
                $student->faculty_id = $oldQuery->faculty_id;
                $student->direction_id = $oldQuery->direction_id;
                $student->course_id = $oldQuery->course_id;
                $student->edu_year_id = $oldQuery->edu_year_id;
                $student->edu_plan_id = $oldQuery->edu_plan_id;
                $student->edu_type_id = $student->eduPlan->edu_type_id;
                $student->edu_form_id = $oldQuery->edu_form_id;
            }
            $student->update(false);
        }

        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
}
