<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\model\Subject;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "teacher_access".
 *
 * @property int $id
 * @property int $user_id
 * @property int $subject_id
 * @property int $language_id
 * @property int $is_lecture
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property Languages $language
 * @property Subject $subject
 * @property User $user
 * @property TimeTable1[] $timeTables
 */
class TeacherAccess extends \yii\db\ActiveRecord
{

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
        return 'teacher_access';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id', 'language_id'], 'required'],
            [['is_lecture', 'user_id', 'subject_id', 'language_id', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
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
            'user_id' => 'User ID',
            'subject_id' => 'Subject ID',
            'language_id' => 'Languages ID',
            'is_lecture' => 'is_lecture',
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
            'teacher' => function ($model) {
                return $model->teacher ?? null;
            },
            'user_id',
            'is_lecture',
            'subject_id',
            'language_id',
            'is_deleted',
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
            'language',
            'subject',
            'subjectAll',
            'teacher',
            'examStudentCount',
            'examStudent',
            'user',

            'subjectCategory',

            'exam',
            'hasContent',
            'content',
            'profile',
            'statisticAttend',

            'contentCount',
            'timetable',
            'timeTableDates',
            'topicContentCount',

            'timeTables',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getTimetable()
    {
        $eduYearId = Yii::$app->request->get('edu_year_id');
        $date = Yii::$app->request->get('date');
        $type = Yii::$app->request->get('type');
        if (isset($type) && $type == 1) {
            return $this->hasMany(TimetableDate::className() , ['teacher_access_id' => 'id'])
                    ->where(['edu_year_id' => $eduYearId, 'status' => 1, 'is_deleted' => 0])
                    ->andWhere(['<' , 'date' , $date])
                    ->orderBy('date asc');
        } elseif (isset($type) && $type == 2) {
            return $this->hasMany(TimetableDate::className() , ['teacher_access_id' => 'id'])
                ->where(['edu_year_id' => $eduYearId, 'status' => 1, 'is_deleted' => 0])
                ->andWhere(['=' , 'date' , $date]);
        } else {
            return $this->hasMany(TimetableDate::className() , ['teacher_access_id' => 'id'])
                ->where(['edu_year_id' => $eduYearId, 'status' => 1, 'is_deleted' => 0])
                ->orderBy('date asc');
        }
    }


    public function getExamStudent()
    {
        return $this->hasMany(ExamStudent::className(), ['teacher_access_id' => 'id']);
    }

    public function getExamStudentCount()
    {
        return count($this->examStudent);
    }

    /**
     * Gets query for [[Languages]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    public function getSubjectCategory()
    {
        return $this->hasOne(SubjectCategory::className(), ['id' => 'is_lecture']);
    }


    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubjectAll()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getSubject() {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id'])->onCondition(['is_deleted' => 0]);
    }

    public function getTimeTableDates()
    {
        return TimetableDate::find()
            ->where([
                'edu_year_id' => Yii::$app->request->get('edu_year_id') ?? activeYearId(),
                'teacher_access_id' => $this->id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->groupBy('date')
            ->all();
    }

    public function getTopicContentCount()
    {
        $topicId = Yii::$app->request->get('subject_topic_id');
        return SubjectContent::find()
            ->where([
                'subject_topic_id' => $topicId,
                'status' => 1,
                'is_deleted' => 0,
                'user_id' => $this->user_id
            ])->count();
    }

    public function getStatisticAttend()
    {
        $activeYear = EduYear::findOne([
            'status' => 1,
            'is_deleted' => 0
        ]);

        $timeTables = TimeTable1::find()
            ->where([
                'teacher_access_id' => $this->id,
                'subject_id' => $this->subject_id,
                'edu_year_id' => $activeYear->id,
                'status' => 1,
                'is_deleted' => 0
            ])
            ->all();

        $data = [];
        if (count($timeTables) > 0) {
            foreach ($timeTables as $timeTable) {
                $dates = $timeTable->attendanceDates;
                $currentDate = Yii::$app->request->get('today') ? Yii::$app->request->get('today') : date("Y-m-d");
                $isCheck = false;
                if (count($dates) > 0) {
                    foreach ($dates as $date => $attend) {
                        if ($currentDate == $date) {
                            $isCheck = true;
                            if ($attend == null) {
                                $data[] = [
                                    'status' => 0,
                                    'date' => $currentDate,
                                    'subject_category' => [
                                        'id' => $timeTable->subject_category_id,
                                        'name' => $timeTable->subjectCategory->translate->name,
                                    ],
                                    'group' => [
                                        'id' => $timeTable->group_id,
                                        'name' => $timeTable->group->unical_name,
                                    ],
                                ];
                            } else {
                                $data[] = [
                                    'status' => 1,
                                    'date' => $currentDate,
                                    'subject_category' => [
                                        'id' => $timeTable->subject_category_id,
                                        'name' => $timeTable->subjectCategory->translate->name,
                                    ],
                                    'group' => [
                                        'id' => $timeTable->group_id,
                                        'name' => $timeTable->group->unical_name,
                                    ],
                                ];
                            }
                        }
                    }
                }
                if (!$isCheck) {
                    $data[] =  [
                        'status' => 2, // Darsi yo'q
                        'date' => $currentDate,
                        'subject_category' => [
                            'id' => $timeTable->subject_category_id,
                            'name' => $timeTable->subjectCategory->translate->name,
                        ],
                        'group' => [
                            'id' => $timeTable->group_id,
                            'name' => $timeTable->group->unical_name,
                        ],
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[Profile]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacher()
    {
        // $data = [];
        // $data['first_name'] = $this->profile->first_name;
        // $data['last_name'] = $this->profile->last_name;
        // $data['middle_name'] = $this->profile->middle_name;

        // return $data;

        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']); //->onCondition(['is_deleted' => 0]); //->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id'])->select(['first_name', 'last_name', 'middle_name']);
    }

    public function getContent()
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere(
                ['user_id' => $this->user_id]
            )
            ->andWhere([
                'in', 'subject_topic_id',
                SubjectTopic::find()->select('id')->where(['subject_id' => $this->subject_id, 'lang_id' => $this->language_id])
            ]);

        $data = $query->all();

        return count($data);
    }

    public function getHasContent()
    {
        $model = new SubjectContent();

        $query = $model->find()
            ->andWhere(
                ['user_id' => $this->user_id]
            )
            ->andWhere([
                'in', 'subject_topic_id',
                SubjectTopic::find()->select('id')->where(['subject_id' => $this->subject_id, 'lang_id' => $this->language_id])
            ]);

        $data = $query->all();

        return count($data);
    }

    public function getContentCount() {
        $subjectTopics = SubjectTopic::find()
            ->where([
                'subject_id' => $this->subject_id,
                'lang_id' => $this->language_id,
                'subject_category_id' => $this->is_lecture,
                'status' => 1,
                'is_deleted' => 0
            ])->all();
        $count = 0;
        if (count($subjectTopics) > 0) {
            foreach ($subjectTopics as $subjectTopic) {
                $content = SubjectContent::find()
                    ->where([
                        'user_id' => $this->user_id,
                        'subject_topic_id' => $subjectTopic->id,
                        'status' => 1,
                        'is_deleted' => 0
                    ])
                    ->all();
                if (count($content) > 0) {
                    $count++;
                }
            }
        }
        return [
            'subject_topic_count' => count($subjectTopics),
            'teacher_topic_count' => $count
        ];
    }


    public function getExam() {
        return Exam::find()
            ->andWhere([
                'in' , 'id' , ExamStudent::find()->select('exam_id')
                    ->where([
                        'exam_teacher_user_id' => $this->user_id,
                        'is_deleted' => 0
                    ])
            ])
            ->all();
    }


    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getTimeTables()
    {
        return $this->hasMany(TimeTable1::className(), ['teacher_access_id' => 'id']);
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($model->save()) {
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
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        /** TeacherAccess */
        if (isset($post['teacher_access'])) {
            // TeacherAccess::updateAll(['status' => 0 , 'is_deleted' => 1], ['user_id' => $model->id]);
            $post['teacher_access'] = str_replace("'", "", $post['teacher_access']);
            $teacher_access = json_decode(str_replace("'", "", $post['teacher_access']));

            foreach ($teacher_access as $teacher_access_key => $teacher_access_value) {
                if (!isset($teacher_access_value)) {
                    $errors[] = ['Lang ID or Subject Category ID' => [_e('Not found')]];
                }
                $subject = Subject::findOne($teacher_access_key);
                if (isset($subject)) {
                    foreach ($teacher_access_value as $langKey => $lecture) {
                        if (!isset($lecture)) {
                            $errors[] = ['Subject Category ID' => [_e('Not found')]];
                        }
                        $isset_lang = Languages::findOne($langKey);
                        if (isset($isset_lang)) {
                            foreach ($lecture as  $subject_category_value) {
                                $subject_category = SubjectCategory::findOne($subject_category_value);
                                if (isset($subject_category)) {
                                    $userAccessBefore = TeacherAccess::findOne([
                                        'user_id' => $model->id,
                                        'subject_id' => $teacher_access_key,
                                        'language_id' => $langKey,
                                        'is_lecture' => $subject_category_value,
                                    ]);
                                    if (!isset($userAccessBefore)) {
                                        $teacherAccessNew = new TeacherAccess();
                                        $teacherAccessNew->user_id = $model->id;
                                        $teacherAccessNew->subject_id = $teacher_access_key;
                                        $teacherAccessNew->language_id = $langKey;
                                        $teacherAccessNew->is_lecture = $subject_category_value;
                                        $teacherAccessNew->save(false);
                                    } else {
                                        $userAccessBefore->status = 1;
                                        $userAccessBefore->is_deleted = 0;
                                        $userAccessBefore->save(false);
                                    }
                                } else {
                                    $errors[] = ['subject_category_id' => [_e('Not found')]];
                                }
                            }
                        } else {
                            $errors[] = ['language_id' => [_e('Not found')]];
                        }
                    }
                } else {
                    $errors[] = ['subject_id' => [_e('Not found')]];
                }
            }
        }
        /** TeacherAccess */


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
            $this->created_by = Current_user_id();
        } else {
            $this->updated_by = Current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
