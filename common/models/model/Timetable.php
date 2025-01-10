<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\TimeTableCreate;
use api\resources\TimeTableUpdate;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "time_table".
 *
 * @property int $id
 * @property int $teacher_access_id
// * @property int $fall_spring
 * @property int $para_id
 * @property int $week_id
 * @property int $course_id
 * @property int $semestr_id
 * @property int $edu_year_id
 * @property int $subject_id
 * @property int $language_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Course $course
 * @property EduYear $eduYear
 * @property Languages $language
 * @property Para $para
 * @property Room $room
 * @property Subject $subject
 * @property Semestr $semestr
 * @property TeacherAccess $teacherAccess
 */
class Timetable extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public $week_id;
    public $para_id;
    public $building_id;
    public $room_id;

    const LECTURE = 1;

    const CONSTANT = 0;

    const ODD_WEEKS = 1;

    const EVEN_WEEKS = 2;



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'timetable';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
//                    'group_id',
//                    'edu_plan_id',
//                    'edu_semestr_id',
//                    'edu_semestr_subject_id',
//                    'faculty_id',
//                    'direction_id',
//                    'building_id',
//                    'room_id',
//                    'week_id',
//                    'para_id',
//                    'subject_category_id',
                ], 'required'
            ],
            [
                [
                    'ids',
                    'group_id',
                    'hour',
                    'room_id',
                    'para_id',
                    'group_id',
                    'course_id',
                    'semestr_id',
                    'edu_year_id',
                    'edu_form_id',
                    'edu_type_id',
                    'subject_id',
                    'edu_plan_id',
                    'edu_semestr_id',
                    'subject_id',
                    'edu_semestr_subject_id',
                    'building_id',
                    'two_group',
                    'type',
                    'group_type',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [
                ['group_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']
            ],
            [
                ['course_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['course_id' => 'id']
            ],
            [
                ['edu_semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestr::className(), 'targetAttribute' => ['edu_semestr_id' => 'id']
            ],
            [
                ['edu_semestr_subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduSemestrSubject::className(), 'targetAttribute' => ['edu_semestr_subject_id' => 'id']
            ],
            [
                ['faculty_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']
            ],
            [
                ['direction_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']
            ],
            [
                ['edu_year_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']
            ],
            [
                ['edu_plan_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']
            ],
            [
                ['para_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Para::className(), 'targetAttribute' => ['para_id' => 'id']
            ],
            [
                ['room_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Room::className(), 'targetAttribute' => ['room_id' => 'id']
            ],
            [
                ['edu_form_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']
            ],
            [
                ['edu_type_id'], 'exist',
                'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']
            ],
            [
                ['week_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Week::className(), 'targetAttribute' => ['week_id' => 'id']
            ],
            [
                ['subject_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']
            ],
            [
                ['semestr_id'], 'exist',
                'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']
            ],
            [
                ['subject_category_id'], 'exist',
                'skipOnError' => true, 'targetClass' => SubjectCategory::className(), 'targetAttribute' => ['subject_category_id' => 'id']
            ],
            ['edu_semestr_id' , 'validateEduSemestrId'],
            ['subject_category_id' , 'validateCategoryId'],
            ['edu_semestr_subject_id', 'validateSubjectId'],
            ['hour', 'validateHour'],
        ];
    }

    public function validateEduSemestrId($attribute, $params)
    {
        $EduSemestrEduPlanId = EduSemestr::findOne($this->edu_semestr_id)->edu_plan_id;
        if ($EduSemestrEduPlanId != $this->edu_plan_id) {
            $this->addError($attribute, _e('Edu Semestr ID does not match the Edu Plan associated with the edu plan.'));
        }
    }

    public function validateHour($attribute, $params)
    {
        if ($this->hour < 0) {
            $this->addError($attribute, _e('The minimum hour cannot be less than 0..'));
        }
    }

    public function validateSubjectId($attribute, $params)
    {
//        $eduSemestrSubject = EduSemestrSubject::findOne([
//            'subject_id' => $this->subject_id,
//            'edu_semestr_id' => $this->group->activeEduSemestr->id,
//            'status' => 1,
//            'is_deleted' => 0
//        ]);
//        if ($eduSemestrSubject == null) {
//            $this->addError($attribute, _e('This subject is not available in the group active semester.'));
//        }
    }

    public function validateCategoryId($attribute, $params)
    {
        if ($this->subject_category_id == self::LECTURE) {
            if ($this->two_group == 1) {
                $this->addError($attribute, _e('It is not possible to divide the group into two groups during the lecture.'));
            }
        }
    }


    public function fields()
    {
        $fields =  [
            'id',
            'ids',
            'hour',
            'faculty_id',
            'direction_id',
            'group_id',
            'edu_form_id',
            'edu_type_id',
            'type',
            'course_id',
            'semestr_id',
            'two_group',
            'group_type',

            'edu_semestr_id',
            'edu_year_id',
            'edu_semestr_subject_id',
            'subject_id',
            'order',
            'edu_plan_id',
            'subject_category_id',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'timeTableDate',
            'attendance',
            'now',
            'subjectType',
            'isStudentBusy',
            'subjectCategory',
            'course',
            'attends',
            'studentAttends',
            'eduYear',
            'timeOption',
            'eduPlan',
            'child',
            'parent',
            'seminar',
            'selected',
            'selectedCount',
            'para',
            'room',
            'week',
            'subject',
            'semestr',
            'teacherAccess',
            'eduSemestr',
            'teacher',
            'building',
            'lecture',
            'allGroup',
            'freeHour',
            'subjectCategoryTime',
            'secondGroup',
            'std',

            'eduSemestrExamsTypes',
            'eduSemestrSubject',
            'studentTimeTables',
            'group',
            'patok',
            'student',
            'twoGroups',
            'attendanceDates',
            'examControl',
            'isLesson',
            'faculty',
            'direction',
            'user',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public static function dayName()
    {
        return [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];
    }

    /**
     * Gets query for [[ExamControls]].
     *
     * @return \yii\db\ActiveQuery|ExamControlQuery
     */
    public function getExamControl()
    {
        return $this->hasOne(ExamControl::className(), ['time_table_id' => 'id']);
    }

    public function getEduSemestrSubject()
    {
        return $this->hasOne(EduSemestrSubject::className(), ['id' => 'edu_semestr_subject_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getStudent()
    {
        return StudentGroup::find()
            ->where([
                'group_id' => $this->group_id,
                'edu_semestr_id' => $this->edu_semestr_id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
    }

    public function getTimeTableStudent()
    {
        if ($this->two_group == 0) {
            $models = StudentGroup::find()
                ->where([
                    'group_id' => $this->group_id,
                    'edu_semestr_id' => $this->edu_semestr_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
        } else {
            $models = TimetableStudent::find()
                ->where([
                    'group_id' => $this->group_id,
                    'ids_id' => $this->ids,
                    'group_type' => $this->group_type,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
        }

        $data = [];
        foreach ($models as $model) {
            $data[] = [
                'id' => $model->student_id,
                'group_id' => $model->group_id,
                'profile' => $model->profile,
                'group' => $model->group,
            ];
        }
        return $data;
    }

    public function getStd()
    {
        if ($this->two_group == 0) {
            $models = StudentGroup::find()
                ->andWhere([
                    'group_id' => $this->group_id,
                    'edu_semestr_id' => $this->edu_semestr_id,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
        } else {
            $models = TimetableStudent::find()
                ->where([
                    'group_id' => $this->group_id,
                    'ids_id' => $this->ids,
                    'group_type' => $this->group_type,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
        }
        return $models;
    }

    public function getSecondGroup()
    {
        if ($this->two_group == 1) {
            $type = 1;
            if ($this->group_type == 1) {
                $type = 2;
            }
            return $this->hasOne(Timetable::className(), ['ids' => 'ids'])->where([
                'group_id' => $this->group_id,
                'group_type' => $type,
                'is_deleted' => 0,
                'status' => 1
            ]);
        }
        return null;
    }

    /**
     * Gets query for [
     * [SubjectCategory]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'subject_category_id']);
    }

    public function getSubjectCategoryTime()
    {
        return EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
            'subject_category_id' => $this->subject_category_id,
            'status' => 1,
            'is_deleted' => 0
        ]);
    }

    public function getFreeHour()
    {
        $allHour = EduSemestrSubjectCategoryTime::findOne([
            'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
            'subject_category_id' => $this->subject_category_id,
            'status' => 1,
            'is_deleted' => 0
        ])->hours;
        $query = TimetableDate::find()
            ->where([
                'group_id' => $this->group_id,
                'edu_semestr_subject_id' => $this->edu_semestr_subject_id,
                'subject_category_id' => $this->subject_category_id,
                'group_type' => 1,
                'status' => 1,
                'is_deleted' => 0
            ])->count();
        $freeHour = ($allHour / 2) - $query;
        if ($freeHour > 0) {
            return $freeHour;
        }
        return 0;
    }

    // o'quv yili id qo'shish kk
    /**
     * Gets query for [[Course]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    /**
     * Gets query for [[Attends]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */

    public function getAttends()
    {
        $date = Yii::$app->request->get('date');

        if (isset($date)) {
            $date = date("Y-m-d", strtotime($date));
            return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->onCondition(['date' => $date])->orderBy('date');
        }

        return $this->hasMany(Attend::className(), ['time_table_id' => 'id'])->orderBy('date');
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getPara()
    {
        return $this->hasOne(Para::className(), ['id' => 'para_id']);
    }

    public function getRoom()
    {
        return $this->hasOne(Room::className(), ['id' => 'room_id']);
    }

    public function getWeek()
    {
        return $this->hasOne(Week::className(), ['id' => 'week_id']);
    }

    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    public function getTeacherAccess()
    {
        return $this->hasOne(TeacherAccess::className(), ['id' => 'teacher_access_id']);
    }

    public function getTimeTableDate()
    {
        return $this->hasMany(TimetableDate::className(), ['timetable_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getAllGroup()
    {
        return $this->hasMany(Timetable::className(), ['ids' => 'ids'])->where(['group_type' => 1,'status' => 1, 'is_deleted' => 0]);
    }



    public function getTeacher()
    {
        return Profile::find()
            ->select(['user_id', 'first_name', 'last_name', 'middle_name'])
            ->where(['user_id' => $this->user_id ?? null])
            ->one();
    }

    /**
     * Gets query for [[EduSemestr]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Building ]].
     *
     * @return \yii\db\ActiveQuery
     */

    public static function createItem($post , $ids) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['groups'])) {
            $post['groups'] = str_replace("'", "", $post['groups']);
            $groups = json_decode(str_replace("'", "", $post['groups']));
            $groups->id = array_unique($groups->id);
            $subject_categoryId = $post['subject_category_id'];
//            if ($subject_categoryId != self::LECTURE) {
//                if (count($groups->id) != 1) {
//                    $errors[] = ['groups' => _e('You cannot attach multiple groups.')];
//                }
//            }
            if ($subject_categoryId == self::LECTURE && $post['two_group'] == 1) {
                $errors[] = ['subject_category_id' => _e('In the lecture class, the group is not divided.')];
            }

            if (count($errors) > 0) {
                $transaction->rollBack();
                return simplify_errors($errors);
            }

//            $ids = (int) round(microtime(true) * 1000);
//            $ids = 1;
//            $timeTable = Timetable::find()->orderBy('ids desc')->one();
//            if ($timeTable) {
//                $ids = $timeTable->ids + 1;
//            }

            $semestrSubject = EduSemestrSubject::findOne($post['edu_semestr_subject_id']);
            if ($semestrSubject->faculty_id == 6) {
                $post['hour'] = 1;
                $post['type'] = 0;
            }


            $t = false;
            switch ($post['type']) {
                case 0:
                    $t = true;
                    if ($post['two_group'] == 0) {
                        $result = TimeTableCreate::switchOne($ids, $post, $groups);

                    } elseif ($post['two_group'] == 1) {
                        $result = TimeTableCreate::switchOneTwoGroup($ids, $post, $groups);
                    }
                    break;
                case 1:
                    $t = true;
                    if ($post['two_group'] == 0) {
                        $result = TimeTableCreate::switchSecond($ids, $post, $groups);
                    } elseif ($post['two_group'] == 1) {
                        $result = TimeTableCreate::switchSecondTwoGroup($ids, $post, $groups);
                    }
                    break;
                case 2:
                    $t = true;
                    if ($post['two_group'] == 0) {
                        $result = TimeTableCreate::switchThree($ids, $post, $groups);
                    } elseif ($post['two_group'] == 1) {
                        $result = TimeTableCreate::switchThreeTwoGroup($ids, $post, $groups);
                    }
                    break;
                default:
                    $errors[] = ['type' , _e('The type value is invalid.')];
                    break;
            }
            if ($t) {
                if ($result['is_ok']) {
                    $transaction->commit();
                    return true;
                } else {
                    $transaction->rollBack();
                    return simplify_errors($result['errors']);
                }
            }
        } else {
            $errors[] = ['groups' => _e('Groups not found.')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function addDay($models , $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($post['type'] == 1) {
            $i = 0;
            foreach ($models as $model) {
                $timeTableDays = TimetableDate::find()
                    ->where([
                        'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                        'subject_category_id' => $model->subject_category_id,
                        'group_id' => $model->group_id,
                        'group_type' => 1,
                        'status' => 1,
                        'is_deleted' => 0
                    ])->count();
                $allHour = EduSemestrSubjectCategoryTime::findOne([
                    'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                    'subject_category_id' => $model->subject_category_id,
                    'status' => 1,
                    'is_deleted' => 0
                ]);
                if (!$allHour) {
                    $errors[] = _e('No hours are allotted for this subject.');
                } else {

                    if ((($allHour->hours / 2) < ($timeTableDays + $post['hour'])) && $i == 0) {
                        $errors[] = ['hour' => _e('Total hours exceeded maximum hours.')];
                    } else {
                        $thisDescTimeTableDate = TimetableDate::find()
                            ->where(['timetable_id' => $model->id , 'status' => 1, 'is_deleted' => 0])
                            ->orderBy('date desc')
                            ->one();
                        $type = 1;
                        if ($model->type != 0) {
                            $type = 2;
                        }
                        $model->hour = $model->hour + $post['hour'];
                        $model->update(false);
                        $dateFrom = new \DateTime($thisDescTimeTableDate->date);
                        for ($i = 1; $i <= $post['hour']; $i++) {
                            $dateFrom->modify('+'.$type.' week');
                            $new = new TimetableDate();
                            $new->timetable_id = $model->id;
                            $new->ids_id = $model->ids;
                            $new->date = $dateFrom->format('Y-m-d');
                            $new->room_id = $thisDescTimeTableDate->room_id;
                            $new->building_id = $thisDescTimeTableDate->building_id;
                            $new->week_id = $thisDescTimeTableDate->week_id;
                            $new->para_id = $thisDescTimeTableDate->para_id;
                            $new->group_id = $model->group_id;
                            $new->edu_semestr_subject_id = $thisDescTimeTableDate->edu_semestr_subject_id;
                            $new->teacher_access_id = $thisDescTimeTableDate->teacher_access_id;
                            $new->user_id = $thisDescTimeTableDate->user_id;
                            $new->subject_id = $thisDescTimeTableDate->subject_id;
                            $new->subject_category_id = $thisDescTimeTableDate->subject_category_id;
                            $new->edu_plan_id  = $thisDescTimeTableDate->edu_plan_id;
                            $new->edu_semestr_id  = $thisDescTimeTableDate->edu_semestr_id;
                            $new->edu_form_id = $thisDescTimeTableDate->edu_form_id;
                            $new->edu_year_id = $thisDescTimeTableDate->edu_year_id;
                            $new->edu_type_id = $thisDescTimeTableDate->edu_type_id;
                            $new->faculty_id = $thisDescTimeTableDate->faculty_id;
                            $new->direction_id = $thisDescTimeTableDate->direction_id;
                            $new->semestr_id = $thisDescTimeTableDate->semestr_id;
                            $new->course_id = $thisDescTimeTableDate->course_id;
                            $new->group_type = $thisDescTimeTableDate->group_type;
                            $new->type = $thisDescTimeTableDate->type;
                            $new->two_group = $thisDescTimeTableDate->two_group;
                            if ($new->validate()) {
                                $new->save(false);
                            } else {
                                $errors[] = $new->errors;
                                return ['is_ok' => false , 'errors' => $errors];
                            }
                        }
                    }
                }
                $i++;
            }
        } elseif ($post['type'] == 2) {
            foreach ($models as $model) {
                $timeTableDays = TimetableDate::find()
                    ->where([
                        'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                        'subject_category_id' => $model->subject_category_id,
                        'group_id' => $model->group_id,
                        'group_type' => 1,
                        'status' => 1,
                        'is_deleted' => 0
                    ])->count();
                $allHour = EduSemestrSubjectCategoryTime::findOne([
                    'edu_semestr_subject_id' => $model->edu_semestr_subject_id,
                    'subject_category_id' => $model->subject_category_id,
                    'status' => 1,
                    'is_deleted' => 0
                ]);
                if (!$allHour) {
                    $errors[] = _e('No hours are allotted for this subject.');
                } else {
                    if (($allHour->hours / 2) < ($timeTableDays + $post['hour'])) {
                        $errors[] = ['hour' => _e('Total hours exceeded maximum hours.')];
                    } else {
                        $thisDescTimeTableDate = TimetableDate::find()
                            ->where(['timetable_id' => $model->id , 'status' => 1, 'is_deleted' => 0])
                            ->orderBy('date desc')
                            ->one();
                        $type = 1;
                        if ($model->type != 0) {
                            $type = 2;
                        }
                        $model->hour = $model->hour + $post['hour'];
                        $model->update(false);
                        $dateFrom = new \DateTime($thisDescTimeTableDate->date);
                        for ($i = 1; $i <= $post['hour']; $i++) {
                            $dateFrom->modify('+'.$type.' week');
                            $new = new TimetableDate();
                            $new->timetable_id = $model->id;
                            $new->ids_id = $model->ids;
                            $new->date = $dateFrom->format('Y-m-d');
                            $new->room_id = $thisDescTimeTableDate->room_id;
                            $new->building_id = $thisDescTimeTableDate->building_id;
                            $new->week_id = $thisDescTimeTableDate->week_id;
                            $new->para_id = $thisDescTimeTableDate->para_id;
                            $new->group_id = $model->group_id;
                            $new->edu_semestr_subject_id = $thisDescTimeTableDate->edu_semestr_subject_id;
                            $new->teacher_access_id = $thisDescTimeTableDate->teacher_access_id;
                            $new->user_id = $thisDescTimeTableDate->user_id;
                            $new->subject_id = $thisDescTimeTableDate->subject_id;
                            $new->subject_category_id = $thisDescTimeTableDate->subject_category_id;
                            $new->edu_plan_id  = $thisDescTimeTableDate->edu_plan_id;
                            $new->edu_semestr_id  = $thisDescTimeTableDate->edu_semestr_id;
                            $new->edu_form_id = $thisDescTimeTableDate->edu_form_id;
                            $new->edu_year_id = $thisDescTimeTableDate->edu_year_id;
                            $new->edu_type_id = $thisDescTimeTableDate->edu_type_id;
                            $new->faculty_id = $thisDescTimeTableDate->faculty_id;
                            $new->direction_id = $thisDescTimeTableDate->direction_id;
                            $new->semestr_id = $thisDescTimeTableDate->semestr_id;
                            $new->course_id = $thisDescTimeTableDate->course_id;
                            $new->group_type = $thisDescTimeTableDate->group_type;
                            $new->type = $thisDescTimeTableDate->type;
                            $new->two_group = $thisDescTimeTableDate->two_group;
                            if ($new->validate()) {
                                $new->save(false);
                            } else {
                                $errors[] = $new->errors;
                                return ['is_ok' => false , 'errors' => $errors];
                            }
                        }

                    }
                }
            }
        } else {
            $errors[] = _e('Type not found.');
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($id, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $t = false;
        switch ($post['type']) {
            case 1:
                $t = true;
                $result = TimeTableUpdate::switchOne($id, $post);
                break;
            case 2:
                $t = true;
                $result = TimeTableUpdate::switchTwo($id, $post);
                break;
            case 4:
                $t = true;
                $result = TimeTableUpdate::switchFour($id, $post);
                break;
            case 5:
                $t = true;
                $result = TimeTableUpdate::switchFive($id, $post);
                break;
            default:
                $errors[] = ['type' , _e('The type value is invalid.')];
                break;
        }

        if ($t) {
            if ($result['is_ok']) {
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($result['errors']);
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function deleteItem($models) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        foreach ($models as $model) {
            $model->is_deleted = 1;
            $model->update(false);
            $timeTableDates = $model->timeTableDate;
            if (count($timeTableDates)) {
                foreach ($timeTableDates as $timeTableDate) {
                    $timeTableDate->is_deleted = 1;
                    $timeTableDate->update(false);

                    if (isRole('admin') || isRole('edu_admin')) {
                        TimetableAttend::updateAll(['status' => 0 , 'is_deleted' => 1] , ['timetable_date_id' =>  $timeTableDate->id]);
                    } else {
                        if ($timeTableDate->attend_status == 1) {
                            $errors[] = _e('The date of attendance is available!');
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function deleteItemOne($model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->is_deleted = 1;
        $model->update(false);
        $timeTableDates = $model->timeTableDate;
        if (count($timeTableDates)) {
            foreach ($timeTableDates as $timeTableDate) {
                $timeTableDate->is_deleted = 1;
                $timeTableDate->update(false);

                if (!(isRole('admin') || isRole('edu_admin'))) {
                    $attend = TimetableAttend::findOne([
                        'timetable_date_id' =>  $timeTableDate->id,
                    ]);
                    if ($attend) {
                        $errors[] = _e('The date of attendance is available!');
                    }
                }

            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function studentType($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $timeTableStudents = TimetableStudent::find()
            ->where([
                'ids_id' => $post['ids_id'],
                'status' => 1,
                'is_deleted' => 0
            ])
            ->andWhere(['in', 'student_id' , array_unique(json_decode($post['student_ids']))])
            ->all();

        foreach ($timeTableStudents as $student) {
            if ($student->group_type == 1) {
                $student->group_type = 2;
            } else {
                $student->group_type = 1;
            }
            $student->save(false);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function addGroup($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['ids'])) {
            $timeTables = Timetable::find()
                ->where([
                    'ids' => $post['ids'],
                    'subject_category_id' => 1,
                    'status' => 1,
                    'is_deleted' => 0
                ])->all();
            if (count($timeTables) > 0) {
                $oneTimeTable = null;
                foreach ($timeTables as $timeTable) {
                    if ($timeTable->group_id == $post['group_id']) {
                        $errors[] = _e('Error!');
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    $oneTimeTable = $timeTable;
                }
                $group = Group::findOne([
                    'status' => 1,
                    'is_deleted' => 0,
                    'id' => $post['group_id']
                ]);
                if ($group) {
                    $activeSemestr = $group->activeEduSemestr;
                    $eduSemestrSubject = EduSemestrSubject::findOne([
                        'edu_semestr_id' => $activeSemestr->id,
                        'subject_id' => $oneTimeTable->subject_id,
                        'status' => 1,
                        'is_deleted' => 0
                    ]);
                    if ($eduSemestrSubject) {
                        $dates = $oneTimeTable->timeTableDate;
                        if (count($dates) != $oneTimeTable->hour) {
                            $errors[] = _e('Dates count errors.');
                        } else {
                            $new = new Timetable();
                            $new->ids = $oneTimeTable->ids;
                            $new->group_id = $group->id;
                            $new->edu_semestr_subject_id = $eduSemestrSubject->id;
                            $new->subject_id = $eduSemestrSubject->subject_id;
                            $new->subject_category_id = 1;
                            $new->edu_plan_id = $activeSemestr->edu_plan_id;
                            $new->edu_semestr_id = $activeSemestr->id;
                            $new->edu_form_id = $activeSemestr->edu_form_id;
                            $new->edu_year_id = $activeSemestr->edu_year_id;
                            $new->edu_type_id = $activeSemestr->edu_type_id;
                            $new->faculty_id = $activeSemestr->faculty_id;
                            $new->direction_id = $activeSemestr->direction_id;
                            $new->semestr_id = $activeSemestr->semestr_id;
                            $new->course_id = $activeSemestr->course_id;
                            $new->type = $oneTimeTable->type;
                            $new->group_type = $oneTimeTable->group_type;
                            $new->hour = $oneTimeTable->hour;
                            if ($new->validate()) {
                                if ($new->save(false)) {
                                    $eduSemestrCategoryTime = EduSemestrSubjectCategoryTime::findOne([
                                        'edu_semestr_subject_id' => $eduSemestrSubject->id,
                                        'subject_category_id' => 1,
                                        'status' => 1,
                                        'is_deleted' => 0
                                    ]);
                                    if ($eduSemestrCategoryTime) {
                                        $createHour = TimetableDate::find()
                                            ->where([
                                                'edu_semestr_subject_id' => $eduSemestrSubject->id,
                                                'subject_category_id' => 1,
                                                'group_id' => $group->id,
                                                'group_type' => 1,
                                                'status' => 1,
                                                'is_deleted' => 0
                                            ])->count();
                                        $allHour = ($eduSemestrCategoryTime->hours / 2) - $createHour;
                                        if ($allHour >= $new->hour) {

                                            foreach ($dates as $date) {
                                                $new2 = new TimetableDate();
                                                $new2->timetable_id = $new->id;
                                                $new2->ids_id = $new->ids;
                                                $new2->date = $date->date;
                                                $new2->building_id = $date->building_id;
                                                $new2->room_id = $date->room_id;
                                                $new2->week_id = $date->week_id;
                                                $new2->para_id = $date->para_id;
                                                $new2->group_id = $new->group_id;
                                                $new2->edu_semestr_subject_id = $new->edu_semestr_subject_id;
                                                $new2->teacher_access_id = $date->teacher_access_id;
                                                $new2->user_id = $date->user_id;
                                                $new2->subject_id = $date->subject_id;
                                                $new2->subject_category_id = 1;
                                                $new2->edu_plan_id = $new->edu_plan_id;
                                                $new2->edu_semestr_id = $new->edu_semestr_id;
                                                $new2->edu_form_id = $new->edu_form_id;
                                                $new2->edu_year_id = $new->edu_year_id;
                                                $new2->edu_type_id = $new->edu_type_id;
                                                $new2->faculty_id = $new->faculty_id;
                                                $new2->direction_id = $new->direction_id;
                                                $new2->semestr_id = $new->semestr_id;
                                                $new2->course_id = $new->course_id;
                                                $new2->group_type = 1;
                                                if ($new2->validate()) {
                                                    $new2->save(false);
                                                } else {
                                                    $errors[] = $new2->errors;
                                                }
                                            }

                                        } else {
                                            $errors[] = _e('Hour error.');
                                        }
                                    } else {
                                        $errors[] = _e('Edu Semestr Subject Category Time not found.');
                                    }

                                } else {
                                    $errors[] = $new->errors;
                                }
                            }
                        }
                    } else {
                        $errors[] = _e('Group subject not found.');
                    }
                } else {
                    $errors[] = _e('Group not found.');
                }
            }
        }
        
        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }


}
