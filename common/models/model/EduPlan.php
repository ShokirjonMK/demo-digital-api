<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%edu_plan}}".
 *
 * @property int $id
 * @property string|null $spring_end
 * @property string|null $spring_start
 * @property string|null $fall_end
 * @property string|null $fall_start
 * @property int $course
 * @property int $semestr
 * @property int $edu_year_id
 * @property int $faculty_id
 * @property int $direction_id
 * @property int $edu_type_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 * @property int|null $edu_form_id ta-lim shakli
 *
 * @property AttendAccess[] $attendAccesses
 * @property AttendReason[] $attendReasons
 * @property Attend[] $attends
 * @property AttentAccess[] $attentAccesses
 * @property Direction $direction
 * @property EduSemestr[] $eduSemestrs
 * @property EduType $eduType
 * @property EduYear $eduYear
 * @property ExamControlStudent[] $examControlStudents
 * @property ExamControl[] $examControls
 * @property Faculty $faculty
 * @property StudentAttend[] $studentAttends
 * @property StudentClub[] $studentClubs
 * @property StudentSubjectSelection[] $studentSubjectSelections
 * @property StudentTimeOption[] $studentTimeOptions
 * @property Student[] $students
 * @property TimeOption[] $timeOptions
 */
class EduPlan extends \yii\db\ActiveRecord
{

    use ResourceTrait;

    public static $selected_language = 'uz';

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
        return 'edu_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'course',
                    'semestr',
                    'edu_year_id',
                    'faculty_id',
                    'direction_id',
                    'edu_type_id',
                    'fall_start',
                    'fall_end',
                    'spring_start',
                    'spring_end'
                ], 'required'
            ],
            [
                [
                    'edu_form_id',
                    'course',
                    'semestr',
                    'edu_year_id',
                    'faculty_id',
                    'direction_id',
                    'edu_type_id',
                    'order',
                    'status',
                    'created_at',
                    'updated_at',
                    'created_by',
                    'updated_by',
                    'is_deleted'
                ], 'integer'
            ],
            [[
                'fall_start',
                'fall_end', 'spring_start', 'spring_end'
            ], 'safe'],
            [['direction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Direction::className(), 'targetAttribute' => ['direction_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course' => 'Course',
            'semestr' => 'Semestr',
            'edu_year_id' => 'Edu Year ID',
            'faculty_id' => 'Faculty ID',
            'direction_id' => 'Direction ID',
            'edu_type_id' => 'Edu Type ID',
            'edu_form_id' => 'Edu Form ID',
            'order' => _e('Order'),
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
            'is_deleted' => _e('Is Deleted'),
            'fall_start' => 'fall smester start date',
            'fall_end' => 'fall smester end date',
            'spring_start' => 'spring smester start date',
            'spring_end' => 'spring smester end date',
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'faculty_id',
            'semestr',
            'edu_year_id',
            'direction_id',
            'edu_type_id',
            'edu_form_id',
            'course',
            'semestr',
            'fall_start',
            'fall_end',
            'spring_start',
            'spring_end',
            'order',
            'status',
            'is_deleted',
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
            'direction',
            'eduYear',
            'faculty',
            'eduForm',
            'eduType',

            'eduSemestrs',


            'student',
            'studentsByLang',
            'studentCount',
            'studentsByLang',
            'studentUzCount',
            'studentEngCount',
            'studentRuCount',
            'studentUz',
            'studentEng',


            'studentRu',
            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    /**
     * For translating 
     */
    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
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


    public function getStudent()
    {
        return $this->hasMany(Student::className(), ['edu_plan_id' => 'id']);
    }
    public function getStudentCount()
    {
        return count($this->student);
    }

    public function getStudentsByLang()
    {
        return [
            "UZ"    => ['count' => count($this->studentUz)],
            "ENG"   => [count($this->studentEng)],
            "RU"    => [count($this->studentRu)],

        ];
    }

    public  function getStudentUzCount()
    {
        return count($this->studentUz);
    }
    public  function getStudentEngCount()
    {
        return count($this->studentEng);
    }

    public  function getStudentRuCount()
    {
        return count($this->studentRu);
    }

    public  function getStudentUz()
    {
        $model = new Student();
        $query = $model->find();

        $query = $query->andWhere(['edu_plan_id' => $this->id]);
        $query = $query->andWhere(['edu_lang_id' => 1]);
        $query = $query->andWhere(['is_deleted' => 0]);
        return $query->all();
    }

    public  function getStudentEng()
    {
        $model = new Student();
        $query = $model->find();

        $query = $query->andWhere(['edu_plan_id' => $this->id]);
        $query = $query->andWhere(['edu_lang_id' => 2]);
        $query = $query->andWhere(['is_deleted' => 0]);

        return $query->all();
    }

    public  function getStudentRu()
    {
        $model = new Student();
        $query = $model->find();

        $query = $query->andWhere(['edu_plan_id' => $this->id]);
        $query = $query->andWhere(['edu_lang_id' => 3]);
        $query = $query->andWhere(['is_deleted' => 0]);

        return $query->all();
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
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
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
     * Gets query for [[EduType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }

    /**
     * Gets query for [[eduForm]].
     *edu_form_id
     * @return \yii\db\ActiveQuery
     */
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }

    /**
     * Gets query for [[EduSemestrs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrs()
    {
        return $this->hasMany(EduSemestr::className(), ['edu_plan_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $has_error = Translate::checkingAll($post);

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }

        if ($has_error['status']) {
            $eduPlan = EduPlan::findOne([
                'faculty_id' => $model->faculty_id,
                'direction_id' => $model->direction_id,
                'edu_type_id' => $model->edu_type_id,
                'edu_form_id' => $model->edu_form_id,
                'edu_year_id' => $model->edu_year_id,
                'is_deleted' => 0
            ]);
            if ($eduPlan) {
                $errors[] = _e('This Edu Plan already exists');
                $transaction->rollBack();
                return simplify_errors($errors);
            }
            if ($model->save()) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }

                $eduYear = [];
                for ($i = 0; $i < $post['course']; $i++) {
                    /* Kuzgi semestrni qo`shish */
                    $newEduSmester = new EduSemestr();

                    $newEduSmester->start_date = date('Y-m-d', strtotime('+' . $i . ' years', strtotime($post['fall_start'])));
                    $newEduSmester->end_date = date('Y-m-d', strtotime('+' . $i . ' years', strtotime($post['fall_end'])));
                    $newEduSmester->edu_plan_id = $model->id;
                    $newEduSmester->course_id = $i + 1;
                    $newEduSmester->status = 0;
                    $newEduSmester->semestr_id = ($i + 1) * 2 - 1;
                    $eduYear[$i] = EduYear::findOne(['year' => date('Y', strtotime($newEduSmester->start_date)), 'type' => 1]);
                    if (!isset($eduYear[$i])) {
                        $eduYear[$i] = new EduYear();
                        $data = [];
                        $eduYear[$i]->year = date('Y', strtotime($newEduSmester->start_date));
                        $data['name'][Yii::$app->request->get('lang')] = $eduYear[$i]->year . '-' . date('Y', strtotime('+1 years', strtotime($newEduSmester->start_date)));
                        $res = EduYear::createItem($eduYear[$i], $data);
                        if (is_array($res)) {
                            $errors[] = _e('Error on creating EduYear');
                            // $model->delete();
                            // return $res;
                        }
                    }

                    $newEduSmester->edu_year_id = $eduYear[$i]->id;

                    $teacherCheckingType = TeacherCheckingType::findOne(['edu_year_id' => $newEduSmester->edu_year_id, 'semestr_id' => 1]);
                    if ($teacherCheckingType) {
                        $newEduSmester->type = $teacherCheckingType->type;
                    }

                    if (!$newEduSmester->validate()) {
                        $errors[] = $newEduSmester->errors;
                    }
                    $newEduSmester->save();
                    /* Kuzgi semestrni qo`shish */

                    /* Baxorgi semestrni qo`shish */
                    $newEduSmester1 = new EduSemestr();
                    $newEduSmester1->start_date = date('Y-m-d', strtotime('+' . $i . ' years', strtotime($post['spring_start'])));
                    $newEduSmester1->end_date = date('Y-m-d', strtotime('+' . $i . ' years', strtotime($post['spring_end'])));
                    $newEduSmester1->edu_plan_id = $model->id;
                    $newEduSmester1->course_id = $i + 1;
                    $newEduSmester1->status = 0;
                    $newEduSmester1->semestr_id = ($i + 1) * 2;

                    $eduYear[$i] = EduYear::findOne(['year' => date('Y', strtotime($newEduSmester->start_date)), 'type' => 2]);
                    $newEduSmester1->edu_year_id = $eduYear[$i]->id;

                    $teacherCheckingType = TeacherCheckingType::findOne(['edu_year_id' => $newEduSmester1->edu_year_id, 'semestr_id' => 1]);
                    if ($teacherCheckingType) {
                        $newEduSmester1->type = $teacherCheckingType->type;
                    }

                    if (!$newEduSmester1->validate()) {
                        $errors[] = $newEduSmester1->errors;
                    }
                    $newEduSmester1->save();
                    /* Baxorgi semestrni qo`shish */
                }

                if (count($errors) > 0) {
                    // $model->delete();
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
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
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
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
