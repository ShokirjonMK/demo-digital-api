<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use common\models\model\SubjectTopic;

use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "subject".
 *
 * @property int $id
 * @property string $name
 * @property int $kafedra_id
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduSemestrSubject[] $eduSemestrSubjects
 * @property Kafedra $kafedra
 * @property TeacherAccess[] $teacherAccesses
 * @property TimeTable1[] $timeTables
 */
class Subject extends \yii\db\ActiveRecord
{

    public static $selected_language = 'uz';

    use ResourceTrait;

    const CREDIT_TIME = 30;


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
        return 'subject';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [
                [
                    'kafedra_id',
                    'type',
                    'subject_type_id',
                    'edu_type_id',
                    'edu_form_id',
                    'edu_semestr_exams_types',
                    'edu_semestr_subject_category_times'
                ],
                'required'],
            [['subject_type_id','kafedra_id', 'all_time' , 'question_count', 'parent_id','edu_type_id','edu_form_id', 'type', 'order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['credit','max_ball'],'double'],
            [['kafedra_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kafedra::className(), 'targetAttribute' => ['kafedra_id' => 'id']],
            [['semestr_id'], 'exist', 'skipOnError' => true, 'targetClass' => Semestr::className(), 'targetAttribute' => ['semestr_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['edu_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduForm::className(), 'targetAttribute' => ['edu_form_id' => 'id']],
            [['edu_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduType::className(), 'targetAttribute' => ['edu_type_id' => 'id']],
            [['subject_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectType::className(), 'targetAttribute' => ['subject_type_id' => 'id']],
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'description' => function ($model) {
                return $model->translate->description ?? '';
            },
            'kafedra_id',
            'parent_id',
            'type',
            'subject_type_id',
            'semestr_id',
            'edu_type_id',
            'edu_form_id',
            'edu_semestr_exams_types',
            'edu_semestr_subject_category_times',
            'credit',
            'auditory_time',
            'max_ball',
            'all_time' ,
            'question_count',

            'is_deleted',
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
        $extraFields = [
            'subjectSillabus',
            'semestr',
            'child',
            'parent',
            'timeTables',
            'teacherAccesses',
            'kafedra',
            'eduForm',
            'eduType',
            'semestrSubjects',
            'description',
            'subjectType',

            'exam',
            'examCount',

            'eduSemestrSubject',
            'examStudentByLang',
            'eduSemestrSubjects',

            'questions',
            'questionsCount',
            'questionsByLang',

            'questionUzCount',
            'questionEngCount',
            'questionRuCount',

            'hasContent',
            'topics',
            'topicsCount',

            'surveyAnswers',
            'surveyAnswersSum',
            'surveyAnswersCount',
            'surveyAnswerAverage',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    public function getExam()
    {
        return Exam::find()->where([
            'in', 'edu_semestr_subject_id',
            EduSemestrSubject::find()
                ->where(['subject_id' => $this->id])
                ->select('id')
                ?? 0
        ])->all();
    }

    public function getQuestionsByLang()
    {
        return [
            "UZ"    => ['count' => count($this->questionUz)],
            "ENG"   => [count($this->questionEng)],
            "RU"    => [count($this->questionRu)],

        ];
    }

    public  function getQuestionUzCount()
    {
        return count($this->questionUz);
    }

    public  function getQuestionEngCount()
    {
        return count($this->questionEng);
    }

    public  function getQuestionRuCount()
    {
        return count($this->questionRu);
    }

    public  function getHasContent()
    {
        $model = new SubjectContent();
        $query = $model->find();

        $query = $query->andWhere([
            'in', $model->tableName() . '.subject_topic_id',
            SubjectTopic::find()->select('id')->where(['subject_id' => $this->id])
        ]);

        if (count($query->all()) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public  function getQuestionUz()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 1]);
        $query = $query->andWhere(['is_deleted' => 0]);
        return $query->all();
    }

    public  function getQuestionEng()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 2]);
        $query = $query->andWhere(['is_deleted' => 0]);

        return $query->all();
    }

    public  function getQuestionRu()
    {
        $model = new Question();
        $query = $model->find();

        $query = $query->andWhere(['subject_id' => $this->id]);
        $query = $query->andWhere(['lang_id' => 3]);
        $query = $query->andWhere(['is_deleted' => 0]);

        return $query->all();
    }

    public function getTopics()
    {
        return $this->hasMany(SubjectTopic::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getTopicsCount() {
        return count($this->topics);
    }
    public function getSubjectCategory()
    {
        return $this->hasMany(SubjectTopic::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSurveyAnswers()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSurveyAnswersSum()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0])->sum('ball');
    }

    public function getSurveyAnswersCount()
    {
        return $this->hasMany(SurveyAnswer::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0])->count();
    }

    public function getSurveyAnswerAverage()
    {
        if ($this->surveyAnswersCount > 0) {
            // return (float) $this->surveyAnswersSum / $this->surveyAnswersCount;
            return round(((float) $this->surveyAnswersSum / $this->surveyAnswersCount), 2);

        }
        return 0;
    }

    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['subject_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getQuestionActive()
    {
        return $this->hasMany(Question::className(), ['subject_id' => 'id'])->onCondition(['status' => 1, 'is_deleted' => 0]);
    }

    public function getQuestionsCount()
    {
        return count($this->questions);
    }

    public function getExamStudentByLang()
    {
        return ExamForSubject::find()->where(['edu_semestr_subject_id' => $this->eduSemestrSubject->id ?? 0])->all();
    }

    public function getExamCount()
    {
        return count($this->exam);
    }


    public function getEduSemestrSubject()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['subject_id' => 'id'])->where(['is_deleted' => 0]);
    }

    public function getSubject()
    {
        return $this->eduSemestrSubject->subject->name ?? "";
    }


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


    /**
     * Gets query for [[EduSemestrSubjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEduSemestrSubjects()
    {
        return $this->hasMany(EduSemestrSubject::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[Kafedra]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKafedra()
    {
        return $this->hasOne(Kafedra::className(), ['id' => 'kafedra_id']);
    }
    public function getEduType()
    {
        return $this->hasOne(EduType::className(), ['id' => 'edu_type_id']);
    }
    public function getEduForm()
    {
        return $this->hasOne(EduForm::className(), ['id' => 'edu_form_id']);
    }
    public function getSubjectType()
    {
        return $this->hasOne(SubjectType::className(), ['id' => 'subject_type_id']);
    }


    /**
     * Gets query for [[Semestr]].
     *semestr_id
     * @return \yii\db\ActiveQuery
     */
    public function getSemestr()
    {
        return $this->hasOne(Semestr::className(), ['id' => 'semestr_id']);
    }

    /**
     * Gets query for [[Parent]].
     *parent_id
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Subject::className(), ['id' => 'parent_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[Child]].
     *child
     * @return \yii\db\ActiveQuery
     */
    public function getChild()
    {
        return $this->hasMany(Subject::className(), ['id' => 'parent_id'])->onCondition(['is_deleted' => 0]);
    }

    /**
     * Gets query for [[TeacherAccesses]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getTeacherAccesses()
    {
        return $this->hasMany(TeacherAccess::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[TimeTables]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTimeTables()
    {
        return $this->hasMany(TimeTable1::className(), ['subject_id' => 'id']);
    }

    /**
     * Gets query for [[SubjectSillabus]].
     *
     * @return \yii\db\ActiveQuery
     */

    public function getName()
    {
        return $this->translate->name ?? '';
    }

    public static function createItem($model , $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (isset($post))

        $has_error = Translate::checkingAll($post);

        if ($has_error['status']) {

            $json_errors = [];
            $post['edu_semestr_exams_types'] = str_replace("'", "", $post['edu_semestr_exams_types']);
            if (!isJsonMK($post['edu_semestr_exams_types'])) {
                $json_errors['edu_semestr_exams_types'] = [_e('Must be Json')];
            }

            $post['edu_semestr_subject_category_times'] = str_replace("'", "", $post['edu_semestr_subject_category_times']);
            if (!isJsonMK($post['edu_semestr_subject_category_times'])) {
                $json_errors['edu_semestr_subject_category_times'] = [_e('Must be Json')];
            }

            if (count($json_errors) > 0) {
                $errors[] = $json_errors;
            }

            $auditory_time = 0;
            $all_time = 0;
            foreach (json_decode($post['edu_semestr_subject_category_times']) as $subject_category_key => $subject_category_value) {
                $subjectCategory = SubjectCategory::findOne($subject_category_key);
                if ($subjectCategory) {
                    if ($subjectCategory->type == SubjectCategory::AUDITORY_TIME) {
                        $auditory_time += $subject_category_value;
                    }
                    $all_time += $subject_category_value;
                } else {
                    $errors[] = [_e('Category time not found')];
                }
            }
            $model->auditory_time = $auditory_time;
            if ($all_time != $model->credit * self::CREDIT_TIME) {
                $errors[] = [_e("Total hours do not equal credit hours.")];
            }

            $max_ball = 0;
            foreach (json_decode($post['edu_semestr_exams_types']) as $exams_types_key => $exams_types_value) {
                $examType = ExamsType::findOne($exams_types_key);
                if ($examType) {
                    $max_ball += $exams_types_value;
                } else {
                    $errors[] = [_e('Exams type not found')];
                }
            }

            $model->max_ball = $max_ball;
            if ($model->max_ball != 100) {
                $errors[] = [_e("The total score must be 100.")];
            }

            $model->edu_semestr_subject_category_times = $post['edu_semestr_subject_category_times'];
            $model->edu_semestr_exams_types = $post['edu_semestr_exams_types'];

            if ($model->save(false)) {
                if (isset($post['description'])) {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                } else {
                    Translate::createTranslate($post['name'], $model->tableName(), $model->id);
                }
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
        }


        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }
        $transaction->rollBack();
        return simplify_errors($errors);

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

            if (isset($post['edu_semestr_exams_types'])) {
                $post['edu_semestr_exams_types'] = str_replace("'", "", $post['edu_semestr_exams_types']);
                if (!isJsonMK($post['edu_semestr_exams_types'])) {
                    $errors[] = [_e('Must be Json')];
                } else {
                    $model->edu_semestr_exams_types = $post['edu_semestr_exams_types'];
                    $max_ball = 0;
                    foreach (json_decode($post['edu_semestr_exams_types']) as $exams_types_key => $exams_types_value) {
                        $examType = ExamsType::findOne($exams_types_key);
                        if ($examType) {
                            $max_ball += $exams_types_value;
                        } else {
                            $errors[] = [_e('Exams type not found')];
                        }
                    }
                    $model->max_ball = $max_ball;
                    if ($model->max_ball != 100) {
                        $errors[] = [_e("The total score must be 100.")];
                    }
                }
            }


            if (isset($post['edu_semestr_subject_category_times'])) {
                $post['edu_semestr_subject_category_times'] = str_replace("'", "", $post['edu_semestr_subject_category_times']);
                if (!isJsonMK($post['edu_semestr_subject_category_times'])) {
                    $errors[] = [_e('Must be Json')];
                } else {
                    $model->edu_semestr_subject_category_times = $post['edu_semestr_subject_category_times'];
                    $auditory_time = 0;
                    $all_time = 0;
                    foreach (json_decode($post['edu_semestr_subject_category_times']) as $subject_category_key => $subject_category_value) {
                        $subjectCategory = SubjectCategory::findOne($subject_category_key);
                        if ($subjectCategory) {
                            if ($subjectCategory->type == SubjectCategory::AUDITORY_TIME) {
                                $auditory_time += $subject_category_value;
                            }
                            $all_time += $subject_category_value;
                        } else {
                            $errors[] = [_e('Category time not found')];
                        }
                    }
                    $model->auditory_time = $auditory_time;
                    if ($all_time != $model->credit * self::CREDIT_TIME) {
                        $errors[] = [_e("Total hours do not equal credit hours.")];
                    }
                }
            }


            if ($model->save(false)) {
                if (isset($post['name'])) {
                    if (isset($post['description'])) {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id, $post['description']);
                    } else {
                        Translate::updateTranslate($post['name'], $model->tableName(), $model->id);
                    }
                }
            } else {
                $transaction->rollBack();
                return simplify_errors($errors);
            }
        } else {
            $transaction->rollBack();
            return double_errors($errors, $has_error['errors']);
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
