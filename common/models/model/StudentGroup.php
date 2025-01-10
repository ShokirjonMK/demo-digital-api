<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\SemestrUpdate;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property int $group_id
 * @property int $student_id
 * @property int $start_date
 * @property string $end_date
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction $direction
 * @property Faculty $faculty
 * @property EduPlan $eduPlan

 */
class StudentGroup extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student_group';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            ['semestr_key' , 'unique'],
            [['group_id', 'student_id', 'edu_plan_id', 'edu_year_id' , 'edu_semestr_id'], 'required'],
            [['semestr_key'] , 'string' , 'max' => 255],
            [['faculty_id','direction_id','group_id','student_id','edu_form_id' ,'course_id' , 'semestr_id' , 'edu_plan_id', 'edu_year_id' , 'edu_semestr_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => Group::className(), 'targetAttribute' => ['group_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'student_id' => _e('Student ID'),
            'group_id' => _e('Group ID'),
            'start_date' => _e('Start Date'),
            'end_date' => _e('End Date'),
            'status' => _e('Status'),
            'order' => _e('Order'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        $fields =  [
            'id',

            'group_id',
            'student_id',
            'edu_plan_id',
            'edu_year_id' ,
            'edu_semestr_id',
            'edu_form_id' ,
            'course_id' ,
            'semestr_id' ,
            'faculty_id',
            'direction_id',

            'status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'is_deleted',
        ];

        return $fields;
    }


    public function extraFields()
    {
        $extraFields =  [
            'faculty',
            'direction',
            'eduPlan',
            'eduYear',
            'eduSemestr',
            'eduForm',
            'semestr',
            'course',
            'group',
            'profile',
            'student',
            'studentSemestrSubjects',
            'studentAcademikReference',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * Gets query for [[Direction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(Direction::className(), ['id' => 'direction_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    public function getEduSemestr()
    {
        return $this->hasOne(EduSemestr::className(), ['id' => 'edu_semestr_id']);
    }

    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }

    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }

    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'course_id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    public function getProfile()
    {
        return Profile::findOne([
            'user_id' => $this->student->user_id
        ]);
    }

    public function getGroup()
    {
        return $this->hasOne(Group::className(), ['id' => 'group_id']);
    }

    public function getStudentSemestrSubjects()
    {
        return StudentSemestrSubject::find()
            ->where([
                'student_id' => $this->student_id,
                'edu_semestr_id' => $this->edu_semestr_id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
    }


    public function getStudentAcademikReference()
    {
        return StudentSemestrSubject::find()
            ->where([
                'student_id' => $this->student_id,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
    }

    /**
     * Group createItem <$model, $post>
     */

    public static function createItem($post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $t = false;
        switch ($post['type']) {
            case 1:
                $t = true;
                $result = SemestrUpdate::typeOne($post);
                break;
            case 2:
                $result = SemestrUpdate::typeTwo($post);
                $t = true;
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
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function updateItem($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['group_id'])) {
            $group = Group::findOne([
                'id' => $post['group_id'],
                'status' => 1,
                'is_deleted' => 0
            ]);
            if (!$group) {
                $errors[] = _e("New Group not found.");
            } else {

                if ($model->group_id == $post['group_id']) {
                    $errors[] = [_e('Errors')];
                } else {
                    $activeSemestr = $group->eduPlan->activeSemestr;
                    $student = $model->student;

                    TimetableStudent::updateAll(['is_deleted' => 1 , 'status' => 0] , ['student_id' => $model->student_id, 'group_id' => $model->group_id, 'is_deleted' => 0]);
                    TimetableAttend::updateAll(['is_deleted' => 1 , 'status' => 0] , ['student_id' => $model->student_id, 'group_id' =>  $model->group_id, 'is_deleted' => 0]);

                    if ($model->course_id == $activeSemestr->course_id) {
                        if ($model->edu_semestr_id == $activeSemestr->id) {
                            $model->group_id = $group->id;
                            $model->save(false);
                            $student->group_id = $group->id;
                            $student->save(false);
                            StudentSemestrSubjectVedomst::updateAll(['group_id' => $group->id] , ['student_id' => $model->student_id, 'edu_year_id' => $model->edu_semestr_id, 'is_deleted' => 0]);
                            StudentMark::updateAll(['group_id' => $group->id] , ['student_id' => $model->student_id, 'edu_semestr_id' => $model->edu_semestr_id, 'is_deleted' => 0]);
                        } else {

                            StudentSemestrSubject::updateAll(['is_deleted' => 1] , ['student_id' => $model->student_id, 'edu_semestr_id' => $model->edu_semestr_id, 'is_deleted' => 0]);
                            StudentSemestrSubjectVedomst::updateAll(['is_deleted' => 1] , ['student_id' => $model->student_id, 'edu_year_id' => $model->edu_semestr_id, 'is_deleted' => 0]);
                            StudentMark::updateAll(['is_deleted' => 1] , ['student_id' => $model->student_id, 'edu_semestr_id' => $model->edu_semestr_id, 'is_deleted' => 0]);

                            $model->group_id = $group->id;
                            $model->edu_year_id = $activeSemestr->edu_year_id;
                            $model->edu_plan_id = $activeSemestr->edu_plan_id;
                            $model->edu_semestr_id = $activeSemestr->id;
                            $model->edu_form_id = $activeSemestr->edu_form_id;
                            $model->semestr_id = $activeSemestr->semestr_id;
                            $model->course_id = $activeSemestr->course_id;
                            $model->faculty_id = $activeSemestr->faculty_id;
                            $model->direction_id = $activeSemestr->direction_id;
                            $model->save(false);

                            $student->group_id = $group->id;
                            $student->faculty_id = $activeSemestr->faculty_id;
                            $student->direction_id = $activeSemestr->direction_id;
                            $student->course_id = $activeSemestr->course_id;
                            $student->edu_year_id = $activeSemestr->edu_year_id;
                            $student->edu_type_id = $activeSemestr->edu_type_id;
                            $student->edu_form_id = $activeSemestr->edu_form_id;
                            $student->edu_plan_id = $activeSemestr->edu_plan_id;
                            $student->save(false);

                            $eduSemestrSubject = $activeSemestr->eduSemestrSubjects;
                            $result = SemestrUpdate::new($model , $eduSemestrSubject);
                            if (!$result['is_ok']) {
                                $transaction->rollBack();
                                return simplify_errors($result['errors']);
                            }
                        }

                        $timeTables = Timetable::find()
                            ->where([
                                'group_id' => $student->group_id,
                                'edu_semestr_id' => $model->edu_semestr_id,
                                'two_group' => 1,
                                'group_type' => 1,
                                'status' => 1,
                                'is_deleted' => 0
                            ])->all();

                        if (count($timeTables) > 0) {
                            foreach ($timeTables as $timeTable) {
                                $new = new TimetableStudent();
                                $new->ids_id = $timeTable->ids;
                                $new->group_id = $student->group_id;
                                $new->student_id = $model->student_id;
                                $new->student_user_id = $new->student->user_id;
                                $new->group_type = 1;
                                $new->save(false);
                            }
                        }
                    } else {
                        $errors[] = [_e('Course Errors')];
                    }
                }
            }
        } else {
            $errors[] = ['group_id' => _e("Group Id not found.")];
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
            $this->semestr_key = $this->generateUniqueKey();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }

    protected function generateUniqueKey()
    {
        $micTime = (int) round(microtime(true) * 1000);
        $startKey = Yii::$app->security->generateRandomString(3);
        $endKey = Yii::$app->security->generateRandomString(3);
        return $startKey.$micTime.$endKey;
    }
}
