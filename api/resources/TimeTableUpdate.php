<?php

namespace api\resources;

use common\models\model\EduSemestrSubjectCategoryTime;
use common\models\model\Timetable;
use common\models\model\Timetable as CommonTimeTable;
use common\models\model\TimetableAttend;
use common\models\model\TimetableDate;
use common\models\SubjectInfo;
use Yii;

class TimeTableUpdate extends CommonTimeTable
{
    use ResourceTrait;

    public static function isTeacher($model)
    {
        $isTeacher = TimetableDate::find()
            ->where([
                'date' => $model->date,
                'para_id' => $model->para_id,
                'user_id' => $model->user_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
            ->exists();
        if ($isTeacher) {
            return true;
        }
        return false;
    }

    public static function isRoom($model)
    {
        $isRoom = TimetableDate::find()
            ->where([
                'date' => $model->date,
                'room_id' => $model->room_id,
                'para_id' => $model->para_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->andWhere(['<>' , 'id' , $model->id])
            ->exists();
        if ($isRoom) {
            return true;
        }
        return false;
    }

    public static function isGroup($model)
    {
        $isRoom = TimetableDate::find()
            ->where([
                'group_id' => $model->group_id,
                'date' => $model->date,
                'para_id' => $model->para_id,
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
            ->exists();
        if ($isRoom) {
            return true;
        }
        return false;
    }
    

    public static function switchOne($id , $post)
    {
        $errors = [];

        $post['dates'] = str_replace("'", "", $post['dates']);
        $timeTableDates = json_decode(str_replace("'", "", $post['dates']));

        foreach ($timeTableDates as $timeTableDate) {
            $models = TimetableDate::find()
                ->where([
                    'para_id' => $timeTableDate->para_id,
                    'date' => $timeTableDate->date,
                    'ids_id' => $id,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->andWhere(['in' , 'group_type' , [1,2]])
                ->all();

            if (count($models) != 2) {
                $errors[] = _e('Errors!');
            } else {
                $first_group = false;
                $second_group = false;
                foreach ($models as $model) {
                    if ($model->group_type == 1) {
                        $first_group = true;
                    } elseif ($model->group_type == 2) {
                        $second_group = true;
                    }
                    if ($post['group_type'] == $model->group_type) {
                        $model->teacher_access_id = $post['teacher_access_id'];
                        $model->room_id = $post['room_id'];
                        $model->building_id = $model->room->building_id;
                        $model->user_id = $model->teacherAccess->user_id;
                    }
                    $model->para_id = $post['para_id'];


                    $isTeacher = TimeTableUpdate::isTeacher($model);
                    if ($isTeacher) {
                        $errors[] = _e('This teacher is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $isRoom = TimetableDate::find()
                        ->where([
                            'date' => $model->date,
                            'room_id' => $model->room_id,
                            'para_id' => $model->para_id,
                            'status' => 1,
                            'is_deleted' => 0,
                        ])
                        ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                        ->exists();
                    if ($isRoom) {
                        $errors[] = _e('This room is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $isGroup = TimetableDate::find()
                        ->where([
                            'group_id' => $model->group_id,
                            'date' => $model->date,
                            'para_id' => $model->para_id,
                            'status' => 1,
                            'is_deleted' => 0,
                        ])
                        ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                        ->exists();

                    if ($isGroup) {
                        $errors[] = _e('This group is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $model->update(false);
                }
                if (!($first_group && $second_group)) {
                    $errors[] = _e('Errors!');
                }
            }
        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function switchTwo($id , $post)
    {
        $errors = [];

        $dates = json_decode($post['dates']);

        foreach ($dates as $date) {
            $models = TimetableDate::find()
                ->where([
                    'date' => $date->date,
                    'para_id' => $date->para_id,
                    'ids_id' => $id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            if (count($models) > 0) {
                foreach ($models as $model) {
                    if ($model->two_group == 0) {

                        $model->teacher_access_id = $post['teacher_access_id'];
                        $model->room_id = $post['room_id'];
                        $model->para_id = $post['para_id'];
                        $model->user_id = $model->teacherAccess->user_id;
                        $model->building_id = $model->room->building_id;

                        $isTeacher = TimeTableUpdate::isTeacher($model);
                        if ($isTeacher) {
                            $errors[] = _e('This teacher is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isRoom = TimetableDate::find()
                            ->where([
                                'date' => $model->date,
                                'room_id' => $model->room_id,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();
                        if ($isRoom) {
                            $errors[] = _e('This room is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isGroup = TimetableDate::find()
                            ->where([
                                'group_id' => $model->group_id,
                                'date' => $model->date,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();

                        if ($isGroup) {
                            $errors[] = _e('This group is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }
                        $model->update(false);

                    } else {
                        $errors[] = _e('Type errors.');
                    }
                }
            } else {
                $errors[] = _e('Data not found.');
            }

        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function switchThree($id , $post)
    {
        $errors = [];

        $dates = json_decode($post['dates']);

        dd($dates);

        foreach ($dates->date as $date) {
            $models = TimetableDate::find()
                ->where([
                    'date' => $date,
                    'ids_id' => $id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();

            if (count($models) > 0) {
                foreach ($models as $model) {
                    if ($model->two_group == 0) {

                        $model->teacher_access_id = $post['teacher_access_id'];
                        $model->room_id = $post['room_id'];
                        $model->para_id = $post['para_id'];
                        $model->user_id = $model->teacherAccess->user_id;
                        $model->building_id = $model->room->building_id;

                        $isTeacher = TimeTableUpdate::isTeacher($model);
                        if ($isTeacher) {
                            $errors[] = _e('This teacher is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isRoom = TimetableDate::find()
                            ->where([
                                'date' => $model->date,
                                'room_id' => $model->room_id,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();
                        if ($isRoom) {
                            $errors[] = _e('This room is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isGroup = TimetableDate::find()
                            ->where([
                                'group_id' => $model->group_id,
                                'date' => $model->date,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();

                        if ($isGroup) {
                            $errors[] = _e('This group is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        if (!$model->validate()) {
                            $errors[] = $model->errors;
                            return ['is_ok' => false , 'errors' => $errors];
                        }
                        $model->update(false);


                    } else {
                        $errors[] = _e('Type errors.');
                    }
                }
            } else {
                $errors[] = _e('Data not found.');
            }

        }


        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function switchFour($id , $post)
    {
        $errors = [];

        $date = $post['date'];

        $models = TimetableDate::find()
            ->where([
                'date' => $date,
                'ids_id' => $id,
                'para_id' => $post['old_para_id'], // qoshildi
                'status' => 1,
                'is_deleted' => 0
            ])->all();

        if (count($models) > 0) {
            foreach ($models as $model) {
                if ($model->two_group == 1) {
                    $errors[] = _e('Errors.');
                } else {
                    $model->date = $post['new_date'];
                    $dateFrom = new \DateTime($post['new_date']);
                    $weekId = $dateFrom->format('N');
                    $model->para_id = $post['para_id'];
                    $model->room_id = $post['room_id'];
                    $model->week_id = $weekId;
                    $model->building_id = $model->room->building_id;
                    $model->teacher_access_id = $post['teacher_access_id'];
                    $model->user_id = $model->teacherAccess->user_id;

                    $isRoom = TimetableDate::find()
                        ->where([
                            'date' => $model->date,
                            'room_id' => $model->room_id,
                            'para_id' => $model->para_id,
                            'status' => 1,
                            'is_deleted' => 0,
                        ])
                        ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                        ->exists();
                    if ($isRoom) {
                        $errors[] = _e('This room is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $isTeacher = TimeTableUpdate::isTeacher($model);
                    if ($isTeacher) {
                        $errors[] = _e('This teacher is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $isGroup = TimetableDate::find()
                        ->where([
                            'group_id' => $model->group_id,
                            'date' => $model->date,
                            'para_id' => $model->para_id,
                            'status' => 1,
                            'is_deleted' => 0,
                        ])
                        ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                        ->exists();

                    if ($isGroup) {
                        $errors[] = _e('This group is busy on this day.');
                        return ['is_ok' => false , 'errors' => $errors];
                    }

                    $model->update(false);

                    TimetableAttend::updateAll(['date' => $model->date , 'para_id' => $model->para_id] , ['timetable_date_id' => $model->id, 'status' => 1, 'is_deleted' => 0]);

                }
            }
        }

        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }

    public static function switchFive($id , $post)
    {
        $errors = [];

        $date = $post['date'];
        if ($post['room_id'] == $post['second_room_id']) {
            $errors[] = _e('Rooms must be different');
        }

        if ($post['teacher_access_id'] == $post['second_teacher_access_id']) {
            $errors[] = _e('Rooms must be different');
        }

        if (count($errors) == 0) {
            $dateFrom = new \DateTime($post['new_date']);
            $weekId = $dateFrom->format('N');
            $models = TimetableDate::find()
                ->where([
                    'date' => $date,
                    'ids_id' => $id,
                    'para_id' => $post['old_para_id'], // qoshildi,
                    'status' => 1,
                    'is_deleted' => 0,
                ])->all();

            if (count($models) == 2) {
                foreach ($models as $model) {
                    if ($model->two_group != 1) {
                        $errors[] = _e('Errors.');
                    } else {

                        $model->para_id = $post['para_id'];
                        $model->date = $post['new_date'];
                        $model->week_id = $weekId;
                        if ($model->group_type == 1) {
                            $model->room_id = $post['room_id'];
                            $model->building_id = $model->room->building_id;
                            $model->teacher_access_id = $post['teacher_access_id'];
                            $model->user_id = $model->teacherAccess->user_id;
                        } elseif ($model->group_type == 2) {
                            $model->room_id = $post['second_room_id'];
                            $model->building_id = $model->room->building_id;
                            $model->teacher_access_id = $post['second_teacher_access_id'];
                            $model->user_id = $model->teacherAccess->user_id;
                        } else {
                            $errors[] = _e('Errors!');
                        }

                        $isRoom = TimetableDate::find()
                            ->where([
                                'date' => $model->date,
                                'room_id' => $model->room_id,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();
                        if ($isRoom) {
                            $errors[] = _e('This room is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isTeacher = TimeTableUpdate::isTeacher($model);
                        if ($isTeacher) {
                            $errors[] = _e('This teacher is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $isGroup = TimetableDate::find()
                            ->where([
                                'group_id' => $model->group_id,
                                'date' => $model->date,
                                'para_id' => $model->para_id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->andWhere(['<>' , 'ids_id' , $model->ids_id])
                            ->exists();

                        if ($isGroup) {
                            $errors[] = _e('This group is busy on this day.');
                            return ['is_ok' => false , 'errors' => $errors];
                        }

                        $model->update(false);
                        TimetableAttend::updateAll(['date' => $model->date , 'para_id' => $model->para_id] , ['timetable_date_id' => $model->id, 'status' => 1, 'is_deleted' => 0]);
                    }
                }
            } else {
                $errors[] = _e('Errors.');
            }
        }

        if (count($errors) == 0) {
            return ['is_ok' => true];
        } else {
            return ['is_ok' => false , 'errors' => $errors];
        }
    }
}
