<?php

namespace api\resources;

use common\models\model\EduSemestrSubjectCategoryTime;
use common\models\model\StudentGroup;
use common\models\model\Timetable;
use common\models\model\Timetable as CommonTimeTable;
use common\models\model\TimetableDate;
use common\models\model\TimetableStudent;
use common\models\SubjectInfo;
use Yii;

class TimeTableCreate extends CommonTimeTable
{
    use ResourceTrait;

    public static function switchOne($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);

        foreach ($groups->id as $groupId) {
            $model = new Timetable();
            $model->ids = $ids;
            $model->group_id = $groupId;
            $model->hour = $post['hour'];
            $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
            $model->subject_id = $model->eduSemestrSubject->subject_id;
            $model->subject_category_id = $post['subject_category_id'];
            $eduSemestr = $model->eduSemestrSubject->eduSemestr;
            $model->edu_semestr_id = $eduSemestr->id;
            $model->edu_plan_id = $eduSemestr->edu_plan_id;
            $model->edu_form_id = $eduSemestr->edu_form_id;
            $model->edu_year_id = $eduSemestr->edu_year_id;
            $model->edu_type_id = $eduSemestr->edu_type_id;
            $model->faculty_id = $eduSemestr->faculty_id;
            $model->direction_id = $eduSemestr->direction_id;
            $model->semestr_id = $eduSemestr->semestr_id;
            $model->course_id = $eduSemestr->course_id;
            $model->type = $post['type'];
            $model->two_group = $post['two_group'];
            $model->group_type = 1;
            if ($model->validate()) {
                if ($model->save(false)) {
                    if ($eduSemestrCategoryTime) {
                        $createHour = TimetableDate::find()
                            ->where([
                                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                                'subject_category_id' => $model->subject_category_id,
                                'group_id' => $model->group_id,
                                'group_type' => 1,
                                'status' => 1,
                                'is_deleted' => 0
                            ])->count();
                        $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;

                        if ($allHour < $post['hour']) {
                            $post['hour'] = $post['hour'] - $allHour;
                        }

                        $dateFromString = $post['start_date'];
                        $dateFrom = new \DateTime($dateFromString);
                        if ($post['week_id'] != $dateFrom->format('N')) {
                            $dateFrom->modify('next ' . $model->dayName()[$post['week_id']]);
                        }
                        $date = [];
                        for ($i = 1; $i <= $post['hour']; $i++) {
                            $date[] = $dateFrom->format('Y-m-d');
                            $dateFrom->modify('+1 week');
                        }
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $post['room_id'];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $post['teacher_access_id'];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                    } else {
                        $errors[] = _e('This subject category is not included in the plan.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = _e('Data not saved.');
                    return ['is_ok' => false , 'errors' => $errors];
                }
            } else {
                $errors[] = $model->errors;
            }
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
    public static function switchOneTwoGroup($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$eduSemestrCategoryTime) {
            $errors[] = _e('This subject category is not included in the plan.');
            return ['is_ok' => false , 'errors' => $errors];
        }

        foreach ($groups->id as $groupId) {
            $createHour = TimetableDate::find()
                ->where([
                    'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
                    'subject_category_id' => $post['subject_category_id'],
                    'group_id' => $groupId,
                    'group_type' => 1,
                    'status' => 1,
                    'is_deleted' => 0
                ])->count();
            $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;

            if ($allHour < $post['hour']) {
                $post['hour'] = $post['hour'] - $allHour;
            }

            $dateFromString = $post['start_date'];
            $dateFrom = new \DateTime($dateFromString);
            if ($post['week_id'] != $dateFrom->format('N')) {
                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
            }
            $date = [];
            for ($i = 1; $i <= $post['hour']; $i++) {
                $date[] = $dateFrom->format('Y-m-d');
                $dateFrom->modify('+1 week');
            }
            $room = array_unique(json_decode($post['room_id']));
            $teacherAccess = array_unique(json_decode($post['teacher_access_id']));

            for ($i = 1; $i <= 2; $i++) {
                $model = new Timetable();
                $model->ids = $ids;
                $model->group_id = $groupId;
                $model->hour = $post['hour'];
                $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
                $model->subject_id = $model->eduSemestrSubject->subject_id;
                $model->subject_category_id = $post['subject_category_id'];
                $eduSemestr = $model->eduSemestrSubject->eduSemestr;
                $model->edu_semestr_id = $eduSemestr->id;
                $model->edu_plan_id = $eduSemestr->edu_plan_id;
                $model->edu_form_id = $eduSemestr->edu_form_id;
                $model->edu_year_id = $eduSemestr->edu_year_id;
                $model->edu_type_id = $eduSemestr->edu_type_id;
                $model->faculty_id = $eduSemestr->faculty_id;
                $model->direction_id = $eduSemestr->direction_id;
                $model->semestr_id = $eduSemestr->semestr_id;
                $model->course_id = $eduSemestr->course_id;
                $model->type = $post['type'];
                $model->two_group = $post['two_group'];
                $model->group_type = $i;
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $room[$i-1];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $teacherAccess[$i-1];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->group_type = $model->group_type;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                        if ($i == 1) {
                            $students = StudentGroup::find()
                                ->where([
                                    'edu_semestr_id' => $model->edu_semestr_id,
                                    'group_id' => $model->group_id,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ])->all();
                            if (count($students) > 0) {
                                foreach ($students as $student) {
                                    $new = new TimetableStudent();
                                    $new->ids_id = $model->ids;
                                    $new->group_id = $model->group_id;
                                    $new->student_id = $student->student_id;
                                    $new->student_user_id = $student->student->user_id;
                                    $new->group_type = 1;
                                    $new->save(false);
                                }
                            }
                        }


                    } else {
                        $errors[] = _e('Data not saved.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = $model->errors;
                }
            }
            break;
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }


    public static function switchSecond($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);

        foreach ($groups->id as $groupId) {
            $model = new Timetable();
            $model->ids = $ids;
            $model->group_id = $groupId;
            $model->hour = $post['hour'];
            $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
            $model->subject_id = $model->eduSemestrSubject->subject_id;
            $model->subject_category_id = $post['subject_category_id'];
            $eduSemestr = $model->eduSemestrSubject->eduSemestr;
            $model->edu_semestr_id = $eduSemestr->id;
            $model->edu_plan_id = $eduSemestr->edu_plan_id;
            $model->edu_form_id = $eduSemestr->edu_form_id;
            $model->edu_year_id = $eduSemestr->edu_year_id;
            $model->edu_type_id = $eduSemestr->edu_type_id;
            $model->faculty_id = $eduSemestr->faculty_id;
            $model->direction_id = $eduSemestr->direction_id;
            $model->semestr_id = $eduSemestr->semestr_id;
            $model->course_id = $eduSemestr->course_id;
            $model->type = $post['type'];
            $model->two_group = $post['two_group'];
            $model->group_type = 1;
            if ($model->validate()) {
                if ($model->save(false)) {
                    if ($eduSemestrCategoryTime) {
                        $createHour = TimetableDate::find()
                            ->where([
                                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                                'subject_category_id' => $model->subject_category_id,
                                'group_id' => $model->group_id,
                                'group_type' => 1,
                                'status' => 1,
                                'is_deleted' => 0
                            ])->count();
                        $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;
                        if ($allHour < $post['hour']) {
                            $post['hour'] = $post['hour'] - $allHour;
                        }

                        $dateFromString = $post['start_date'];
                        $dateFrom = new \DateTime($dateFromString);

                        if ($post['week'] == 2) {
                            if ($post['week_id'] == $dateFrom->format('N')) {
                                $dateFrom->modify('+1 week');
                            } elseif ($post['week_id'] > $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                                $dateFrom->modify('+1 week');
                            } else {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                            }
                        } elseif ($post['week'] == 1) {
                            if ($post['week_id'] > $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                            } elseif ($post['week_id'] < $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                                $dateFrom->modify('+1 week');
                            }
                        }

                        $date = [];
                        for ($i = 1; $i <= $post['hour']; $i++) {
                            $date[] = $dateFrom->format('Y-m-d');
                            $dateFrom->modify('+2 week');
                        }
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $post['room_id'];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $post['teacher_access_id'];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                    } else {
                        $errors[] = _e('This subject category is not included in the plan.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = _e('Data not saved.');
                    return ['is_ok' => false , 'errors' => $errors];
                }
            } else {
                $errors[] = $model->errors;
            }
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
    public static function switchSecondTwoGroup($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$eduSemestrCategoryTime) {
            $errors[] = _e('This subject category is not included in the plan.');
            return ['is_ok' => false , 'errors' => $errors];
        }
        foreach ($groups->id as $groupId) {
            $createHour = TimetableDate::find()
                ->where([
                    'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
                    'subject_category_id' => $post['subject_category_id'],
                    'group_id' => $groupId,
                    'group_type' => 1,
                    'status' => 1,
                    'is_deleted' => 0
                ])->count();
            $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;
            if ($allHour < $post['hour']) {
                $post['hour'] = $post['hour'] - $allHour;
            }

            $dateFromString = $post['start_date'];
            $dateFrom = new \DateTime($dateFromString);

            if ($post['week'] == 2) {
                if ($post['week_id'] == $dateFrom->format('N')) {
                    $dateFrom->modify('+1 week');
                } elseif ($post['week_id'] > $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                    $dateFrom->modify('+1 week');
                } else {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                }
            } elseif ($post['week'] == 1) {
                if ($post['week_id'] > $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                } elseif ($post['week_id'] < $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                    $dateFrom->modify('+1 week');
                }
            }

            $date = [];
            for ($i = 1; $i <= $post['hour']; $i++) {
                $date[] = $dateFrom->format('Y-m-d');
                $dateFrom->modify('+2 week');
            }
            $room = array_unique(json_decode($post['room_id']));
            $teacherAccess = array_unique(json_decode($post['teacher_access_id']));

            for ($i = 1; $i <= 2; $i++) {
                $model = new Timetable();
                $model->ids = $ids;
                $model->group_id = $groupId;
                $model->hour = $post['hour'];
                $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
                $model->subject_id = $model->eduSemestrSubject->subject_id;
                $model->subject_category_id = $post['subject_category_id'];
                $eduSemestr = $model->eduSemestrSubject->eduSemestr;
                $model->edu_semestr_id = $eduSemestr->id;
                $model->edu_plan_id = $eduSemestr->edu_plan_id;
                $model->edu_form_id = $eduSemestr->edu_form_id;
                $model->edu_year_id = $eduSemestr->edu_year_id;
                $model->edu_type_id = $eduSemestr->edu_type_id;
                $model->faculty_id = $eduSemestr->faculty_id;
                $model->direction_id = $eduSemestr->direction_id;
                $model->semestr_id = $eduSemestr->semestr_id;
                $model->course_id = $eduSemestr->course_id;
                $model->type = $post['type'];
                $model->two_group = $post['two_group'];
                $model->group_type = $i;
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $room[$i-1];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $teacherAccess[$i-1];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->group_type = $model->group_type;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                        if ($i == 1) {
                            $students = StudentGroup::find()
                                ->where([
                                    'edu_semestr_id' => $model->edu_semestr_id,
                                    'group_id' => $model->group_id,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ])->all();
                            if (count($students) > 0) {
                                foreach ($students as $student) {
                                    $new = new TimetableStudent();
                                    $new->ids_id = $model->ids;
                                    $new->group_id = $model->group_id;
                                    $new->student_id = $student->student_id;
                                    $new->student_user_id = $student->student->user_id;
                                    $new->group_type = 1;
                                    $new->save(false);
                                }
                            }
                        }

                    } else {
                        $errors[] = _e('Data not saved.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = $model->errors;
                }
            }
            break;
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }


    public static function switchThree($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);

        foreach ($groups->id as $groupId) {
            $model = new Timetable();
            $model->ids = $ids;
            $model->group_id = $groupId;
            $model->hour = $post['hour'];
            $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
            $model->subject_id = $model->eduSemestrSubject->subject_id;
            $model->subject_category_id = $post['subject_category_id'];
            $eduSemestr = $model->eduSemestrSubject->eduSemestr;
            $model->edu_semestr_id = $eduSemestr->id;
            $model->edu_plan_id = $eduSemestr->edu_plan_id;
            $model->edu_form_id = $eduSemestr->edu_form_id;
            $model->edu_year_id = $eduSemestr->edu_year_id;
            $model->edu_type_id = $eduSemestr->edu_type_id;
            $model->faculty_id = $eduSemestr->faculty_id;
            $model->direction_id = $eduSemestr->direction_id;
            $model->semestr_id = $eduSemestr->semestr_id;
            $model->course_id = $eduSemestr->course_id;
            $model->type = $post['type'];
            $model->two_group = $post['two_group'];
            $model->group_type = 1;
            if ($model->validate()) {
                if ($model->save(false)) {
                    if ($eduSemestrCategoryTime) {
                        $createHour = TimetableDate::find()
                            ->where([
                                'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                                'subject_category_id' => $model->subject_category_id,
                                'group_id' => $model->group_id,
                                'group_type' => 1,
                                'status' => 1,
                                'is_deleted' => 0
                            ])->count();
                        $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;
                        if ($allHour < $post['hour']) {
                            $post['hour'] = $post['hour'] - $allHour;
                        }

                        $dateFromString = $post['start_date'];
                        $dateFrom = new \DateTime($dateFromString);

                        if ($post['week'] == 1) {
                            if ($post['week_id'] == $dateFrom->format('N')) {
                                $dateFrom->modify('+1 week');
                            } elseif ($post['week_id'] > $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                                $dateFrom->modify('+1 week');
                            } else {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                            }
                        } elseif ($post['week'] == 2) {
                            if ($post['week_id'] > $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                            } elseif ($post['week_id'] < $dateFrom->format('N')) {
                                $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                                $dateFrom->modify('+1 week');
                            }
                        }

                        $date = [];
                        for ($i = 1; $i <= $post['hour']; $i++) {
                            $date[] = $dateFrom->format('Y-m-d');
                            $dateFrom->modify('+2 week');
                        }
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $post['room_id'];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $post['teacher_access_id'];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                    } else {
                        $errors[] = _e('This subject category is not included in the plan.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = _e('Data not saved.');
                    return ['is_ok' => false , 'errors' => $errors];
                }
            } else {
                $errors[] = $model->errors;
            }
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
    public static function switchThreeTwoGroup($ids, $post, $groups)
    {
        $errors = [];
        $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
            'subject_category_id' => $post['subject_category_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$eduSemestrCategoryTime) {
            $errors[] = _e('This subject category is not included in the plan.');
            return ['is_ok' => false , 'errors' => $errors];
        }
        foreach ($groups->id as $groupId) {
            $createHour = TimetableDate::find()
                ->where([
                    'edu_semestr_subject_id' => $post['edu_semestr_subject_id'],
                    'subject_category_id' => $post['subject_category_id'],
                    'group_id' => $groupId,
                    'group_type' => 1,
                    'status' => 1,
                    'is_deleted' => 0
                ])->count();
            $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;
            if ($allHour < $post['hour']) {
                $post['hour'] = $post['hour'] - $allHour;
            }

            $dateFromString = $post['start_date'];
            $dateFrom = new \DateTime($dateFromString);

            if ($post['week'] == 1) {
                if ($post['week_id'] == $dateFrom->format('N')) {
                    $dateFrom->modify('+1 week');
                } elseif ($post['week_id'] > $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                    $dateFrom->modify('+1 week');
                } else {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                }
            } elseif ($post['week'] == 2) {
                if ($post['week_id'] > $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                } elseif ($post['week_id'] < $dateFrom->format('N')) {
                    $dateFrom->modify('next ' . Timetable::dayName()[$post['week_id']]);
                    $dateFrom->modify('+1 week');
                }
            }

            $date = [];
            for ($i = 1; $i <= $post['hour']; $i++) {
                $date[] = $dateFrom->format('Y-m-d');
                $dateFrom->modify('+2 week');
            }
            $room = array_unique(json_decode($post['room_id']));
            $teacherAccess = array_unique(json_decode($post['teacher_access_id']));

            for ($i = 1; $i <= 2; $i++) {
                $model = new Timetable();
                $model->ids = $ids;
                $model->group_id = $groupId;
                $model->hour = $post['hour'];
                $model->edu_semestr_subject_id = $post['edu_semestr_subject_id'];
                $model->subject_id = $model->eduSemestrSubject->subject_id;
                $model->subject_category_id = $post['subject_category_id'];
                $eduSemestr = $model->eduSemestrSubject->eduSemestr;
                $model->edu_semestr_id = $eduSemestr->id;
                $model->edu_plan_id = $eduSemestr->edu_plan_id;
                $model->edu_form_id = $eduSemestr->edu_form_id;
                $model->edu_year_id = $eduSemestr->edu_year_id;
                $model->edu_type_id = $eduSemestr->edu_type_id;
                $model->faculty_id = $eduSemestr->faculty_id;
                $model->direction_id = $eduSemestr->direction_id;
                $model->semestr_id = $eduSemestr->semestr_id;
                $model->course_id = $eduSemestr->course_id;
                $model->type = $post['type'];
                $model->two_group = $post['two_group'];
                $model->group_type = $i;
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if (count($date) > 0) {
                            foreach ($date as $value) {
                                $new = new TimetableDate();
                                $new->timetable_id = $model->id;
                                $new->ids_id = $model->ids;
                                $new->date = date('Y-m-d' , strtotime($value));
                                $new->room_id = $room[$i-1];
                                $new->building_id = $new->room->building_id;
                                $new->week_id = $post['week_id'];
                                $new->para_id = $post['para_id'];
                                $new->group_id = $groupId;
                                $new->edu_semestr_subject_id = $model->edu_semestr_subject_id;
                                $new->teacher_access_id = $teacherAccess[$i-1];
                                $new->user_id = $new->teacherAccess->user_id;
                                $new->subject_id = $model->subject_id;
                                $new->subject_category_id = $model->subject_category_id;
                                $new->edu_plan_id  = $model->edu_plan_id;
                                $new->edu_semestr_id  = $model->edu_semestr_id;
                                $new->edu_form_id = $model->edu_form_id;
                                $new->edu_year_id = $model->edu_year_id;
                                $new->edu_type_id = $model->edu_type_id;
                                $new->faculty_id = $model->faculty_id;
                                $new->direction_id = $model->direction_id;
                                $new->semestr_id = $model->semestr_id;
                                $new->course_id = $model->course_id;
                                $new->group_type = $model->group_type;
                                $new->two_group = $post['two_group'];
                                $new->type = $post['type'];
                                if ($new->validate()) {
                                    $new->save(false);
                                } else {
                                    $errors[] = $new->errors;
                                    return ['is_ok' => false , 'errors' => $errors];
                                }
                            }
                        }

                        if ($i == 1) {
                            $students = StudentGroup::find()
                                ->where([
                                    'edu_semestr_id' => $model->edu_semestr_id,
                                    'group_id' => $model->group_id,
                                    'status' => 1,
                                    'is_deleted' => 0
                                ])->all();
                            if (count($students) > 0) {
                                foreach ($students as $student) {
                                    $new = new TimetableStudent();
                                    $new->ids_id = $model->ids;
                                    $new->group_id = $model->group_id;
                                    $new->student_id = $student->student_id;
                                    $new->student_user_id = $student->student->user_id;
                                    $new->group_type = 1;
                                    $new->save(false);
                                }
                            }
                        }

                    } else {
                        $errors[] = _e('Data not saved.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }
                } else {
                    $errors[] = $model->errors;
                }
            }
            break;
        }
        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function twoGroupStudent($model)
    {
        $errors = [];

        $students = StudentGroup::find()
            ->where([
                'edu_semestr_id' => $model->edu_semestr_id,
                'group_id' => $model->group_id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
        if (count($students) > 0) {
            foreach ($students as $student) {
                $new = new TimetableStudent();
                $new->ids_id = $model->ids;
                $new->group_id = $model->group_id;
                $new->student_id = $student->student_id;
                $new->student_user_id = $student->student->user_id;
                $new->group_type = 1;
                $new->save(false);
            }
        }

        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
}
