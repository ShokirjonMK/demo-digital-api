<?php

namespace common\models\model;

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
            [['order', 'user_id', 'turniket_department_id', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            // [['name'], 'string', 'max' => 255],
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
            'turniket_department_id',
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
            'userAccessCount',
            'kafedras',
            'eduPlans',
            'directions',

            'dean',

            'students',
            'studentsCount',

            'studentCountByCourse',
            'studentCountByForm',
            'studentCountByType',




            'notComingByDate',

            'notComingByDateByCourse',
            'test',


            'studentsAll',
            'studentsCountAll', // barcha studentlar o'chkanlariham 

            'attendStudentByDay',
            'attendStudentByDayByForm',
            'studentsCountByForm',


            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getNotComingByDate($course_id = null): int
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        $weekNumber = date('N', strtotime($date));

        $studentQuery = Student::find()
            ->innerJoin(StudentTimeTable::tableName(), 'student_time_table.student_id = student.id')
            ->where(['student_time_table.week_id' => $weekNumber])
            ->andWhere(['student_time_table.archived' => 0])
            ->andWhere(['student.is_deleted' => 0])
            ->andWhere(['student.status' => 10])
            ->andWhere(['student.faculty_id' => $this->id]);

        if (isset($course_id)) {
            $studentQuery->andWhere(['student.course_id' => $course_id]);
        }

        $studentQuery
            ->groupBy('student.id')
            ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0)', [':date' => $date]);

        return $studentQuery->count();
    }

    public function getNotComingByDateByCourse()
    {
        $courses = Course::find()
            ->where(['status' => 1])
            ->all();

        $result = [];
        foreach ($courses as $course) {
            $result[] = [
                'course_id' => $course->id,
                'student_count' => $this->getNotComingByDate($course->id)
            ];
        }

        return $result;
    }


    public function getNotComingByDate1()
    {
        $date = Yii::$app->request->get('date') ? date("Y-m-d", strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        return
            Student::find()
            ->select('student.course_id, COUNT(DISTINCT student.course_id) AS student_count')
            ->innerJoin('student_time_table', 'student_time_table.student_id = student.id')

            ->where(['student_time_table.week_id' => date('N', strtotime($date))])
            ->andWhere(['student_time_table.archived' => 0])
            ->andWhere(['student.faculty_id' => $this->id])

            ->where([
                'student.status' => 10,
                'student.faculty_id' => $this->id,
                'student.is_deleted' => 0,
            ])
            ->andWhere(['!=', 'student.course_id', 9])
            ->groupBy('student.id, student.course_id')
            ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.date = :date AND student_attend.archived = 0)', [':date' => $date])
            ->orderBy('course_id')
            ->asArray()
            ->all();
    }

    public function getNotComingByDateByCourse1()
    {
        // Get the date from the request or use today's date by default
        $formattedDate = Yii::$app->request->get('date') ? date("Y-m-d", strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');
        $weekNumber = date('N', strtotime($formattedDate)); // Get the week day number (1 to 7)

        // Initialize the Student model
        $model = new Student();

        // Build the query
        $query = $model->find()
            ->select([
                'student_time_table.course_id',  // Select course_id
                'COUNT(student.id) AS student_count'  // Count the students
            ])
            ->innerJoin('student_time_table', 'student_time_table.student_id = student.id')
            // ->leftJoin('student_attend', 'student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0', [':date' => $formattedDate])
            ->where(['student_time_table.week_id' => $weekNumber])
            ->andWhere(['student_time_table.archived' => 0])  // Filter out archived timetable entries
            ->andWhere(['student.status' => 10])  // Only active students
            ->andWhere(['student.faculty_id' => $this->id])  // Only students from this faculty
            ->andWhere(['student.is_deleted' => 0])  // Exclude deleted students
            ->groupBy('student_time_table.course_id')  // Group by course_id
            // ->having('COUNT(student_attend.id) = 0')  // Ensure no attendance for the given day
            ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0)', [':date' => $formattedDate])

            ->asArray()  // Return results as an array
            ->all();

        // Return the query result
        return $query;
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
    public function getStudentCountByCourse()
    {
        return Student::find()
            ->select('course_id, COUNT(*) AS student_count')
            ->where([
                'status' => 10,
                'faculty_id' => $this->id,
                'is_deleted' => 0,
            ])
            ->andWhere(['!=', 'course_id', 9])
            ->groupBy('course_id')
            ->orderBy('course_id')
            ->asArray()
            ->all();
    }

    public function getStudentCountByForm()
    {
        return Student::find()
            ->select('edu_form_id, COUNT(*) AS student_count')
            ->where([
                'status' => 10,
                'faculty_id' => $this->id,
                'is_deleted' => 0,
            ])
            ->andWhere(['!=', 'course_id', 9])
            ->groupBy('edu_form_id')
            ->orderBy('edu_form_id')
            ->asArray()
            ->all();
    }

    public function getStudentCountByType()
    {
        return Student::find()
            ->select('edu_type_id, COUNT(*) AS student_count')
            ->where([
                'status' => 10,
                'faculty_id' => $this->id,
                'is_deleted' => 0,
            ])
            ->andWhere(['!=', 'course_id', 9])
            ->groupBy('edu_type_id')
            ->orderBy('edu_type_id')
            ->asArray()
            ->all();
    }

    public function getStudentsForm1()
    {
        return $this->hasMany(Student::className(), ['faculty_id' => 'id'])
            ->onCondition([
                'status' => 10,
                'is_deleted' => 0
            ])
            ->where(['edu_form_id' => 1])
            ->andWhere(['!=', 'course_id', 9]);
    }

    public function getStudentsForm2()
    {
        return $this->hasMany(Student::className(), ['faculty_id' => 'id'])
            ->onCondition([
                'edu_form_id' => 2,
                'status' => 10,
                'is_deleted' => 0
            ])
            ->andWhere(['!=', 'course_id', 9]);
    }

    public function getStudentsCountByForm()
    {
        return [
            'form1' => $this->getStudentsForm1()->count(),
            'form2' => $this->getStudentsForm2()->count()

            // 'form1' => count($this->studentsForm1),
            // 'form2' => count($this->studentsForm2)
        ];
    }

    public function getStudentsAll()
    {
        return $this->hasMany(Student::className(), ['faculty_id' => 'id']);
    }

    public function getStudentsCountAll()
    {
        return count($this->studentsAll);
    }


    public function getDean()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getAttendStudentByDayByForm()
    {
        // Get 'date' from request or use the current date in 'Y-m-d' format
        $date = Yii::$app->request->get('date') ? date("Y-m-d", strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        // Get 'faculty_id' from request, if available
        $course_id = Yii::$app->request->get('course_id');

        // Create query to fetch distinct student count for attendance
        $query1 = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_attend.student_id) AS student_count'
            ])
            ->from('student_attend')
            ->innerJoin('student', 'student_attend.student_id = student.id')
            ->where([
                'student_attend.date' => $date,
                'student_attend.faculty_id' => $this->id,
                'student_attend.archived' => 0,
                'student.status' => 10,

                'student.is_deleted' => 0,
            ])->andWhere(['<>', 'student.edu_form_id', 2]);

        $query2 = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_attend.student_id) AS student_count'
            ])
            ->from('student_attend')
            ->innerJoin('student', 'student_attend.student_id = student.id')
            ->where([
                'student_attend.date' => $date,
                'student_attend.faculty_id' => $this->id,
                'student_attend.archived' => 0,
                'student.status' => 10,
                'student.edu_form_id' => 2,
                'student.is_deleted' => 0,
            ]);

        // Fetch the result

        // return $query->createCommand()->rawsql;
        $result1 = $query1->one();

        // Apply additional faculty filter if 'faculty_id' is set
        if ($course_id) {
            $query1->andWhere(['student_attend.course_id' => $course_id]);
            $query2->andWhere(['student_attend.course_id' => $course_id]);
        }

        // Fetch the result
        $result1 = $query1->one();
        $result2 = $query2->one();

        // Return the student count and the date used in the query
        return [
            'date' => $date,
            'form1' => [
                'student_count' => $result1['student_count'] ?? 0, // If no results, default to 0
            ],
            'form2' => [
                'student_count' => $result2['student_count'] ?? 0, // If no results, default to 0
            ]
        ];
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
            ->innerJoin('student', 'student_attend.student_id = student.id')
            ->where([
                'student_attend.date' => $date,
                'student_attend.faculty_id' => $this->id,
                'student_attend.archived' => 0,
                'student.status' => 10,
                'student.is_deleted' => 0,
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
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }
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


    /**
     * Gets query for [[AttendReasons]].
     *
     * @return \yii\db\ActiveQuery|AttendReasonQuery
     */
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

    public function getUserAccessCount()
    {
        return count($this->userAccess);
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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
