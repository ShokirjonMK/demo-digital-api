<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%attend_reason}}".
 *
 * @property int $id
 * @property string $start
 * @property string $end
 * @property int $student_id
 * @property int|null $subject_id
 * @property int|null $faculty_id
 * @property int|null $edu_plan_id
 * @property string|null $file
 * @property int|null $status
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 * @property EduPlan $eduPlan
 * @property Faculty $faculty
 * @property Student $student
 * @property StudentAttend[] $studentAttends
 * @property Subject $subject
 */

class AttendReason extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const CONFIRMED = 1;
    const NOT_CONFIRMED = 0;
    const CANCELLED = 2;

    const UPLOADS_FOLDER = 'uploads/attend_reason/';
    public $attend_file;
    public $attendFileMaxSize = 1024 * 1024 * 5; // 5 Mb

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'attend_reason';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'start',
                'end',
                'student_id',
                'edu_year_id',
            ], 'required'],
            [[
                'start',
                'end'
            ], 'safe'],
//            [
//                ['end'],
//                'compare',
//                'compareValue' => date("Y-m-d H:i:s", strtotime('-1 month')),
//                'operator' => '>=',
//                'message' => _e('The date must be within the last month.')
//            ],

            [
                ['start'],
                'compare',
                'compareValue' => 'end',
                'operator' => '<',
                'message' => _e('The end time must be greater than the start time.')
            ],

            [[
                'is_confirmed',
                'student_id',
                'subject_id',
                'faculty_id',
                'edu_plan_id',
                'edu_year_id',
                'status',
                'order',
                'created_at',
                'updated_at',
                'created_by',
                'updated_by',
                'is_deleted'
            ], 'integer'],
            [
                ['file'], 'string',
                'max' => 255
            ],
            [
                ['description'], 'string'
            ],
            [['edu_plan_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduPlan::className(), 'targetAttribute' => ['edu_plan_id' => 'id']],
            [['faculty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Faculty::className(), 'targetAttribute' => ['faculty_id' => 'id']],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['subject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subject::className(), 'targetAttribute' => ['subject_id' => 'id']],
            [['edu_year_id'], 'exist', 'skipOnError' => true, 'targetClass' => EduYear::className(), 'targetAttribute' => ['edu_year_id' => 'id']],
            [['attend_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf,doc,docx,png,jpg', 'maxSize' => $this->attendFileMaxSize],

        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),

            'start' => _e('Start'),
            'end' => _e('End'),
            'student_id' => _e('Student ID'),
            'subject_id' => _e('Subject ID'),
            'faculty_id' => _e('Faculty ID'),
            'edu_plan_id' => _e('Edu Plan ID'),
            'file' => _e('File'),

            'status' => _e('Status'),
            'order' => _e('Order'),
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
            'is_confirmed',
            'start' => function($model) {
                return strtotime($this->start);
            },
            'end' => function($model) {
                return strtotime($this->end);
            },
            'student_id',
            'subject_id',
            'faculty_id',
            'edu_plan_id',
            'edu_year_id',
            'file',
            'description',

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
            'eduPlan',
            'faculty',
            'student',
            'studentAttends',
            'subject',
            'eduYear',

            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }


    /**
     * Gets query for [[EduYear]].
     *
     * @return \yii\db\ActiveQuery|EduYearQuery
     */
    public function getEduYear()
    {
        return $this->hasOne(EduYear::className(), ['id' => 'edu_year_id']);
    }
    /**
     * Gets query for [[EduPlan]].
     *
     * @return \yii\db\ActiveQuery|EduPlanQuery
     */
    public function getEduPlan()
    {
        return $this->hasOne(EduPlan::className(), ['id' => 'edu_plan_id']);
    }

    /**
     * Gets query for [[Faculty]].
     *
     * @return \yii\db\ActiveQuery|FacultyQuery
     */
    public function getFaculty()
    {
        return $this->hasOne(Faculty::className(), ['id' => 'faculty_id']);
    }

    /**
     * Gets query for [[Student]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }

    /**
     * Gets query for [[StudentAttends]].
     *
     * @return \yii\db\ActiveQuery|StudentAttendQuery
     */
    public function getStudentAttends()
    {
        return $this->hasMany(StudentAttend::className(), ['attend_reason_id' => 'id']);
    }

    /**
     * Gets query for [[Subject]].
     *
     * @return \yii\db\ActiveQuery|SubjectQuery
     */
    public function getSubject()
    {
        return $this->hasOne(Subject::className(), ['id' => 'subject_id']);
    }



    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->faculty_id = $model->student->faculty_id;
        $model->edu_plan_id = $model->student->edu_plan_id;
//        $model->user_id = $model->student->user_id;

        $model->attend_file = UploadedFile::getInstancesByName('attend_file');
        if ($model->attend_file) {
            $model->attend_file = $model->attend_file[0];
            $attendFileUrl = $model->upload();
            if ($attendFileUrl) {
                $model->file = $attendFileUrl;
            } else {
                $errors[] = $model->errors;
            }
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {

//            $model->attend_file = UploadedFile::getInstancesByName('attend_file');
//            if ($model->attend_file) {
//                $model->attend_file = $model->attend_file[0];
//                $questionFileUrl = $model->uploadFile();
//                if ($questionFileUrl) {
//                    $model->file = $questionFileUrl;
//                } else {
//                    $errors[] = $model->errors;
//                }
//            }

        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function confirmItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->is_confirmed == self::CANCELLED) {
            $errors[] = _e('This has been cancelled!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $reasonStart = date("Y-m-d", strtotime($model->start));
        $reasonEnd = date("Y-m-d", strtotime($model->end));

        $studentAttends = new StudentAttend();
        $studentAttends = $studentAttends::find();
        $studentAttends = $studentAttends
            ->where(['student_id' => $model->student_id])
            ->andWhere(['between' , 'date' , $reasonStart , $reasonEnd])
            ->all();

        foreach ($studentAttends as $studentAttend) {

            $t = false;
            if ($studentAttend->date == $reasonStart) {
                if (($studentAttend->timeTable->para->start_time >= date('H:i', strtotime($model->start)))) {
                    $t = true;
                }
            } elseif ($studentAttend->date == $reasonEnd) {
                if (($studentAttend->timeTable->para->end_time <= date('H:i', strtotime($model->end)))) {
                    $t = true;
                }
            } else {
                $t = true;
            }

            if ($t) {
                $studentAttend->attend_reason_id = $model->id;
                $studentAttend->reason = StudentAttend::REASON_TRUE;

                if (!$studentAttend->update(false)) {
                    $errors[] = $studentAttend->errors;
                }
            }
        }

        $model->is_confirmed = self::CONFIRMED;
        if (!$model->save(false)) {
            $errors[] = $model->errors;
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        }

        $transaction->rollBack();
        return simplify_errors($errors);
    }




    public static function cancellationItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if ($model->is_confirmed == self::CONFIRMED) {
            $errors[] = _e('This reason confirmed!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->is_confirmed = self::CANCELLED;
        if (!$model->save(false)) {
            $errors[] = $model->errors;
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

        if ($model->is_confirmed == self::CONFIRMED) {
            $errors[] = _e('This reason confirmed!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $model->faculty_id = $model->student->faculty_id;
        $model->edu_plan_id = $model->student->edu_plan_id;
        $model->is_confirmed = self::NOT_CONFIRMED;

        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $oldFile = $model->file;
        // attend file saqlaymiz
        $model->attend_file = UploadedFile::getInstancesByName('attend_file');
        if ($model->attend_file) {
            $model->attend_file = $model->attend_file[0];
            $questionFileUrl = $model->uploadFile();
            if ($questionFileUrl) {
                $model->file = $questionFileUrl;
                self::deleteFile($oldFile);
            } else {
                $errors[] = $model->errors;
            }
        }
        // ***

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($model)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        /** Delete reason on Student Attent */
        if ($model->is_confirmed == self::CONFIRMED) {
            $errors[] = _e('This reason confirmed!');
            $transaction->rollBack();
            return simplify_errors($errors);
        }
        /** Logic here */

        if ($model->delete()) {
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

    // 
    public function deleteFile($oldFile = NULL)
    {
        if (isset($oldFile)) {
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }
        return true;
    }

    public function upload()
    {
        if ($this->validate()) {
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }
            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->attend_file->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->attend_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }

    public function uploadFile()
    {
        if ($this->validate()) {
            if (!file_exists(STORAGE_PATH  . self::UPLOADS_FOLDER)) {
                mkdir(STORAGE_PATH  . self::UPLOADS_FOLDER, 0777, true);
            }

            $fileName = $this->id . "_" . \Yii::$app->security->generateRandomString(10) . '.' . $this->attend_file->extension;

            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->attend_file->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }
}
