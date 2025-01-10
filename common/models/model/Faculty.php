<?php

namespace common\models\model;

use api\resources\BaseGet;
use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Direction[] $directions
 * @property EduPlan[] $eduPlans
 * @property Kafedra[] $kafedras
 */
class Faculty extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    const USER_ACCESS_TYPE_ID = 1;

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
        return 'faculty';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            // [['name'], 'required'],
            [['order', 'user_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            // [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            // 'name' => 'Name',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'user_id',
            'order',
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
            'leader',
            'userAccess',
            'kafedras',
            'eduPlans',
            'directions',

            'students',
            'studentsCount',
            'studentsAll',
            'studentsCountAll', // barcha studentlar o'chkanlariham 

            'attendStudentByDay',

            'translate',
            'studentStatistic',
            'groups',
            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getAttendStudentByDay()
    {
        $date = Yii::$app->request->get('date') ?? date('Y-m-d');
        $date = date("Y-m-d", strtotime($date));

        $query = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_id) AS student_count'
            ])
            ->from('student_attend')
            ->where([
                'date' => $date,
                'faculty_id' => $this->id,
            ]);

        $result = $query->one();
        return [
            'student_count' => $result['student_count'],
            'date' => $date,
        ];
        return $result['student_count'];
    }

    public function getAttendStudentByDay001()
    {
        $date = Yii::$app->request->get('date') ?? date('Y-m-d');
        $date = date("Y-m-d", strtotime($date));

        $query = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_id) AS student_count'
            ])
            ->from('student_attend')
            ->where([
                'date' => $date,
                'faculty_id' => $this->id,
            ])
            ->indexBy('faculty_id')
            ->column();

        return $query;
    }

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => Yii::$app->request->get('lang'), 'table_name' => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
        return $this->hasMany(Translate::class, ['model_id' => 'id'])
            ->andOnCondition(['language' => self::$selected_language, 'table_name' => $this->tableName()]);
    }

    /**
     * Get Translate
     *
     * @return void
     */
    public function getTranslate()
    {
//        if (Yii::$app->request->get('self') == 1) {
//            return $this->infoRelation[0];
//        }
        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    /**
     * Gets query for [[Directions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirections()
    {
        return $this->hasMany(Direction::className(), ['faculty_id' => 'id']);
    }

    public function getGroups()
    {
        return $this->hasMany(Group::className(), ['faculty_id' => 'id']);
    }


    /**
     * Gets query for [[AttendReasons]].
     *
     * @return \yii\db\ActiveQuery|AttendReasonQuery
     */


    public function getStudentStatistic() {
        $data = [];
        $data['male'] = BaseGet::studentCount([
            'student.faculty_id' => $this->id,
            'student.gender' => 1,
            'student.status' => 10,
            'student.is_deleted' => 0
        ]);
        $data['woman'] = BaseGet::studentCount([
            'student.faculty_id' => $this->id,
            'student.gender' => 2,
            'student.status' => 10,
            'student.is_deleted' => 0
        ]);

        $data['magister'] = BaseGet::studentCount([
            'student.faculty_id' => $this->id,
            'student.edu_type_id' => 2,
            'student.status' => 10,
            'student.is_deleted' => 0
        ]);

        $data['externally'] = BaseGet::studentCount([
            'student.faculty_id' => $this->id,
            'student.edu_form_id' => 2,
            'student.status' => 10,
            'student.is_deleted' => 0
        ]);

        return $data;
    }

    public function getEmployee() {
        $data = [];

    }

    public function getAttendReasons()
    {
        return $this->hasMany(AttendReason::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[Attends]].
     *
     * @return \yii\db\ActiveQuery|AttendQuery
     */
    public function getAttends()
    {
        return $this->hasMany(Attend::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[EduSemestrSubjects]].
     *
     * @return \yii\db\ActiveQuery|EduSemestrSubjectQuery
     */
    public function getEduSemestrSubjects()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[Exams]].
     *
     * @return \yii\db\ActiveQuery|ExamQuery
     */
    public function getExams()
    {
        return $this->hasMany(Exam::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[EduPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduPlans()
    {
        return $this->hasMany(EduPlan::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[Kafedras]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKafedras()
    {
        return $this->hasMany(Kafedra::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[StudentClubs]].
     *
     * @return \yii\db\ActiveQuery|StudentClubQuery
     */
    public function getStudentClubs()
    {
        return $this->hasMany(StudentClub::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[StudentSubjectSelections]].
     *
     * @return \yii\db\ActiveQuery|StudentSubjectSelectionQuery
     */
    public function getStudentSubjectSelections()
    {
        return $this->hasMany(StudentSubjectSelection::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[StudentTimeOptions]].
     *
     * @return \yii\db\ActiveQuery|StudentTimeOptionQuery
     */
    public function getStudentTimeOptions()
    {
        return $this->hasMany(StudentTimeOption::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[Students]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['faculty_id' => 'id'])
            ->onCondition([
                'status' => 10,
                'is_deleted' => 0
            ])
            ->andWhere(['!=', 'course_id', 9]);
    }

    public function getStudentsCount()
    {
        return count($this->students);
    }

    public function getStudentsAll()
    {
        return $this->hasMany(Student::className(), ['faculty_id' => 'id']);
    }

    public function getStudentsCountAll()
    {
        return count($this->studentsAll);
    }

    /**
     * Gets query for [[TimeOptions]].
     *
     * @return \yii\db\ActiveQuery|TimeOptionQuery
     */
    public function getTimeOptions()
    {
        return $this->hasMany(TimeOption::className(), ['faculty_id' => 'id']);
    }

    /**
     * Gets query for [[Leader]].
     * leader
     * @return \yii\db\ActiveQuery
     */
    public function getLeader()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[UserAccess]].
     * userAccess
     * @return \yii\db\ActiveQuery
     */
    public function getUserAccess()
    {
        if (!isRole('justice'))
            return $this->hasMany(UserAccess::className(), ['table_id' => 'id'])
                ->andOnCondition(['USER_ACCESS_TYPE_ID' => self::USER_ACCESS_TYPE_ID, 'is_deleted' => 0]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
            }
        } else {
            $errors = double_errors($errors, $has_error['errors']);
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        $has_error = Translate::checkingUpdate($post);
        if ($has_error['status']) {
            if ($model->save()) {
                /* update User Access */
                if (isset($post['user_id'])) {
                    $userAccessUser = User::findOne($post['user_id']);
                    if (($userAccessUser)) {
                        if (!(UserAccess::changeLeader($model->id, self::USER_ACCESS_TYPE_ID, $userAccessUser->id))) {
                            $errors = ['user_id' => _e('Error occured on updating UserAccess')];
                        }
                    }
                }
                /* User Access */

                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
            }
        } else {
            $errors = double_errors($errors, $has_error['errors']);
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
