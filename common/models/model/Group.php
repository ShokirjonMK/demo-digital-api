<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "group".
 *
 * @property int $id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $edu_plan_id
 * @property string $unical_name
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
class Group extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';
    
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public $time_table;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['faculty_id', 'direction_id', 'edu_plan_id', 'unical_name', 'language_id'], 'required'],
            [['language_id' , 'faculty_id', 'direction_id', 'edu_plan_id', 'status', 'order', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['unical_name'], 'string', 'max' => 255],
            [['unical_name'], 'unique'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'faculty_id' => _e('Faculty ID'),
            'direction_id' => _e('Direction ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'unical_name' => _e('Unical Name'),
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
//            "name" => function ($model) {
//                return $model->translate->name ?? "";
//            },
            'id',
            'faculty_id',
            'direction_id',
            'edu_plan_id',
            'unical_name',
            'language_id',
            'status',
            'order',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'time_table'
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'direction',
            'faculty',
            'eduPlan',
            'student',
            'subject',
            'subjects',
            'studentCount',
            'languages',
            'activeEduSemestr',
            'semestrStudentCount',
            'deanQrKod',
            'semestrStudent',
            'eduSemestrs',
            'courseTimeTable',
            'semestrSubject',
            'activeCourse',
            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    
    public function getInfoRelation()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => Yii::$app->request->get("lang"), "table_name" => $this->tableName()]);
    }

    public function getInfoRelationDefaultLanguage()
    {
        // self::$selected_language = array_value(admin_current_lang(), "lang_code", "en");
        return $this->hasMany(Translate::class, ["model_id" => "id"])
            ->andOnCondition(["language" => self::$selected_language, "table_name" => $this->tableName()]);
    }

    /**
     * Get Tranlate
     *
     * @return void
     */


    public function getTranslate()
    {
        if (Yii::$app->request->get("self") == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
    }

    public function getDescription()
    {
        return $this->translate->description ?? "";
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

    public function getLanguages()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }

    public function getSemestrSubject()
    {
        $eduSemestrId = Yii::$app->request->get('edu_semestr_id');
        $eduExamId = Yii::$app->request->get('exams_type_id');

        $eduSemestrSubjects = EduSemestrSubject::find()->where([
            'edu_semestr_id' => $eduSemestrId,
            'status' => 1,
            'is_deleted' => 0
        ])->all();

        $data = [];
        foreach ($eduSemestrSubjects as $eduSemestrSubject) {
            $yes_passed = 0;
            $no_passed = 0;

            $studentMarks = StudentMark::find()
                ->where([
                    'is_deleted' => 0,
                    'edu_semestr_subject_id' => $eduSemestrSubject->id,
                    'group_id' => $this->id,
                    'exam_type_id' => $eduExamId
                ])->all();

            if (count($studentMarks) > 0) {
                foreach ($studentMarks as $studentMark) {
                    if (($studentMark->max_ball * 0.6) <= $studentMark->ball) {
                        $yes_passed++;
                    } else {
                        $no_passed++;
                    }
                }
            }

            $data[] = [
                'edu_semestr_subject_id' => $eduSemestrSubject->id,
                'passed' => $yes_passed,
                'no_passed' => $no_passed
            ];
        }

        return $data;
    }

    public function getSemestrStudent()
    {
        return StudentGroup::find()
            ->where([
                'group_id' => $this->id,
                'status' => 1,
                'is_deleted' => 0,
                'edu_year_id' => Yii::$app->request->get('edu_year_id') ?? activeYearId()
            ])->all();
    }

    public function getSemestrStudentCount()
    {
        return count($this->semestrStudent);
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

    public function getDeanQrKod()
    {
        $deanProfile = $this->faculty->leader->profile;
        $txt = "Dekan: ". $deanProfile->last_name." ".$deanProfile->first_name." ".$deanProfile->middle_name. PHP_EOL. PHP_EOL. "Guruh: ".$this->unical_name ;
        return [
            'dean' => [
                'last_name' => $deanProfile->last_name,
                'first_name' => $deanProfile->first_name,
                'middle_name' => $deanProfile->middle_name,
            ],
            'qrCode' => qrCodeMK($txt)
        ];
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

    public function getActiveEduSemestr()
    {
        return EduSemestr::findOne([
            'edu_plan_id' => $this->edu_plan_id,
            'status' => 1,
            'is_deleted' => 0,
            'is_checked' => 1,
        ]);
    }

    public function getEduSemestrs()
    {
        return EduSemestr::find()->where([
            'edu_plan_id' => $this->edu_plan_id,
            'is_deleted' => 0,
        ])->all();
    }

    public function getSubject() {
        return EduSemestrSubject::find()
            ->where([
                'edu_semestr_id' => $this->activeEduSemestr->id,
                'is_checked' => 0 , // keyin 1 ga o'zgartirladi.
                'status' => 1,
                'is_deleted' => 0,
            ])
            ->all();
    }

    public function getSubjects() {
        return Subject::find()
            ->where(['status' => 1, 'is_deleted' => 0])
            ->andWhere([
                'in' , 'id' , TimeTable1::find()
                    ->select('subject_id')
                    ->where([
                        'group_id' => $this->id,
                        'edu_semestr_id' => $this->activeEduSemestr ? $this->activeEduSemestr->id : null,
                        'status' => 1,
                        'is_deleted' => 0,
                    ])
            ])
            ->all();
    }

    public function getCourseTimeTable() {
        $data = [];
        if (isset($this->activeEduSemestr)) {
            $eduSemestrSubjects = EduSemestrSubject::find()
                ->where([
                    'edu_semestr_id' => $this->activeEduSemestr->id,
                    'is_checked' => 0 , // keyin 1 ga o'zgartirladi.
                    'status' => 1,
                    'is_deleted' => 0,
                ])
                ->all();
            if (isset($eduSemestrSubjects)) {
                foreach ($eduSemestrSubjects as $eduSemestrSubject) {
                    $subjectCategory = SubjectCategory::find()->where(['id' => [1 , 2, 3]])->all();
                    foreach ($subjectCategory as $category) {
                        $timeTables = TimeTable1::find()
                            ->where([
                                'group_id' => $this->id,
                                'edu_semestr_id' => $this->activeEduSemestr->id,
                                'subject_id' => $eduSemestrSubject->subject_id,
                                'subject_category_id' => $category->id,
                                'status' => 1,
                                'is_deleted' => 0,
                            ])
                            ->all();
                        if ($timeTables != null) {
                            foreach ($timeTables as $timeTable) {
                                $data[] = $timeTable->id;
                            }
                            break;
                        }
                    }
                }
            }
        }

        $time_table = TimeTable1::find()
            ->where(['id' => $data])
            ->all();

        return $time_table;
    }

    public function getStudent()
    {
        $students = Student::find()
            ->where([
                'group_id' => $this->id,
                'is_deleted' => 0,
                'status' => 10
            ])->all();
        return $students;
//        if (isset($students)) {
//            $data = [];
//            foreach ($students as $student) {
//                if ($student->user->status == 10) {
//                    $data[] = $student;
//                }
//            }
//            return $data;
//        }
        return null;
        // return $this->hasMany(Student::className(), ['group_id' => 'id'])->onCondition(['archived' => 0, 'is_deleted' => 0]);
    }

    public function getStudentCount() {
        return count($this->student);
    }


    /**
     * Group createItem <$model, $post>
     */

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        // some logic for creating

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    /**
     * Group updateItem <$model, $post>
     */
    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

//        Student::updateAll([
//            'faculty_id' => $model->faculty_id,
//            'direction_id' => $model->direction_id,
//            'edu_plan_id' => $model->edu_plan_id,
//            'edu_lang_id' => $model->language_id,
//            'edu_form_id' => $model->eduPlan->edu_form_id,
//            'edu_type_id' => $model->eduPlan->edu_type_id,
//            'edu_year_id' => $model->eduPlan->edu_year_id,
//            'course_id' => $model->eduPlan->activeSemestr->id,
//        ],['group_id' => $model->id]);
        // some logic for updating

        if ($model->save(false)) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function markPercent111($query)
    {
        $models = $query->all();
        $countGroup = count($models);

        $eduExamId = Yii::$app->request->get('edu_semestr_exams_type_id');
        $eduSemestrId = Yii::$app->request->get('edu_semestr_id');
        $eduSemestrSubjectIds = Yii::$app->request->get('edu_semestr_subject_ids');

        $query = EduSemestrSubject::find()->where([
            'edu_semestr_id' => $eduSemestrId,
            'status' => 1,
            'is_deleted' => 0
        ]);

        if ($eduSemestrSubjectIds !== null) {
            $query->andWhere(['id' => $eduSemestrSubjectIds]);
        }
        $eduSemestrSubjects = $query->all();

        $countSubjects = count($eduSemestrSubjects);

        if ($countGroup > 0 && $countSubjects > 0) {

            foreach ($eduSemestrSubjects as $eduSemestrSubject) {
                foreach ($models as $model) {
                    $studentMarks = StudentMark::find()
                        ->where([
                            'is_deleted' => 0,
                            'edu_semestr_subject_id' => $eduSemestrSubject->id,
                            'group_id' => $model->id,
                            'edu_semestr_exams_type_id' => $eduExamId
                        ])->all();

                    if (count($studentMarks) > 0) {
                        foreach ($studentMarks as $studentMark) {

                        }
                    }

                }
            }

        }

        return [
            'countGroup' => 121212,
        ];
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
