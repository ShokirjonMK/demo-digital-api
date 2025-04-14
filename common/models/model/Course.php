<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course".
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
 * @property EduSemestr[] $eduSemestrs
 * @property TimeTable[] $timeTables
 */
class Course extends \yii\db\ActiveRecord
{
    public static $selected_language = 'uz';

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'course';
    }

    public function rules()
    {
        return [
            //            [['name'], 'required'],
            [['order', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            //            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //            'name' => 'Name',
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
            'eduSemestrs',
            'timeTables',
            'attendStudentByDay',
            'students',
            'studentsCount',
            'studentsCountByForm',
            'attendStudentByDayByForm',


            'notComingByDate',

            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getNotComingByDate($faculty_id = null): int
    {
        $date = Yii::$app->request->get('date') ? date('Y-m-d', strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        $weekNumber = date('N', strtotime($date));

        $studentQuery = Student::find()
            // ->innerJoin(StudentTimeTable::tableName(), 'student_time_table.student_id = student.id and student_time_table.course_id = student.course_id')
            ->innerJoin(StudentTimeTable::tableName(), 'student_time_table.student_id = student.id')
            ->where(['student_time_table.week_id' => $weekNumber])
            ->andWhere(['student_time_table.archived' => 0])
            ->andWhere(['student.is_deleted' => 0])
            ->andWhere(['student.status' => 10])
            ->andWhere(['student.course_id' => $this->id]);

        if (isset($faculty_id)) {
            $studentQuery->andWhere(['student.faculty_id' => $faculty_id]);
        }

        $studentQuery
            ->groupBy('student.id')
            ->having('COUNT(student_time_table.id) = (SELECT COUNT(student_attend.id) FROM student_attend WHERE student_attend.student_id = student.id AND student_attend.date = :date AND student_attend.archived = 0)', [':date' => $date]);

        return $studentQuery->count();
    }


    public function getAttendStudentByDay()
    {
        // Get 'date' from request or use the current date in 'Y-m-d' format
        $date = Yii::$app->request->get('date') ? date("Y-m-d", strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        // Get 'faculty_id' from request, if available
        $faculty_id = Yii::$app->request->get('faculty_id');

        // Create query to fetch distinct student count for attendance
        $query = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_attend.student_id) AS student_count'
            ])
            ->from('student_attend')
            ->innerJoin('student', 'student_attend.student_id = student.id')
            ->where([
                'student_attend.date' => $date,
                'student_attend.course_id' => $this->id,
                'student_attend.archived' => 0,
                'student.status' => 10,
                'student.is_deleted' => 0,
            ]);

        // Apply additional faculty filter if 'faculty_id' is set
        if ($faculty_id) {
            $query->andWhere(['student_attend.faculty_id' => $faculty_id]);
        }

        // Fetch the result
        $result = $query->one();

        // Return the student count and the date used in the query
        return [
            'student_count' => $result['student_count'] ?? 0, // If no results, default to 0
            'date' => $date,
        ];
    }

    public function getAttendStudentByDayByForm()
    {
        // Get 'date' from request or use the current date in 'Y-m-d' format
        $date = Yii::$app->request->get('date') ? date("Y-m-d", strtotime(Yii::$app->request->get('date'))) : date('Y-m-d');

        // Get 'faculty_id' from request, if available
        $faculty_id = Yii::$app->request->get('faculty_id');

        // Create query to fetch distinct student count for attendance
        $query1 = (new \yii\db\Query())
            ->select([
                'COUNT(DISTINCT student_attend.student_id) AS student_count'
            ])
            ->from('student_attend')
            ->innerJoin('student', 'student_attend.student_id = student.id')
            ->where([
                'student_attend.date' => $date,
                'student_attend.course_id' => $this->id,
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
                'student_attend.course_id' => $this->id,
                'student_attend.archived' => 0,
                'student.status' => 10,
                'student.edu_form_id' => 2,
                'student.is_deleted' => 0,
            ]);

        // Fetch the result

        // return $query->createCommand()->rawsql;
        $result1 = $query1->one();

        // Apply additional faculty filter if 'faculty_id' is set
        if ($faculty_id) {
            $query1->andWhere(['student_attend.faculty_id' => $faculty_id]);
        }

        if ($faculty_id) {
            $query2->andWhere(['student_attend.faculty_id' => $faculty_id]);
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

    /**
     * Gets query for [[Students]].
     *
     * @return \yii\db\ActiveQuery|StudentQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['course_id' => 'id'])
            ->onCondition([
                'status' => 10,
                'is_deleted' => 0
            ]);
    }

    public function getStudentsCount()
    {
        return count($this->students);
    }

    public function getStudentsForm1()
    {

        $query = $this->hasMany(Student::className(), ['course_id' => 'id'])
            ->onCondition([
                'status' => 10,
                'is_deleted' => 0,
                'edu_form_id' => 1,
            ]);

        if (Yii::$app->request->get('faculty_id')) {
            $query->andWhere(['faculty_id' => Yii::$app->request->get('faculty_id')]);
        }

        return $query;
    }

    public function getStudentsForm2()
    {
        $query = $this->hasMany(Student::className(), ['course_id' => 'id'])
            ->onCondition([
                'status' => 10,
                'is_deleted' => 0,
                'edu_form_id' => 2,
            ]);

        if (Yii::$app->request->get('faculty_id')) {
            $query->andWhere(['faculty_id' => Yii::$app->request->get('faculty_id')]);
        }

        return $query;
    }


    public function getStudentsCountByForm()
    {
        return ['form1' => count($this->studentsForm1), 'form2' => count($this->studentsForm2)];
    }

    public function getTranslate()
    {
        if (Yii::$app->request->get('self') == 1) {
            return $this->infoRelation[0];
        }

        return $this->infoRelation[0] ?? $this->infoRelationDefaultLanguage[0];
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

    public function getDescription()
    {
        return $this->translate->description ?? '';
    }

    public function getEduSemestrs()
    {
        return $this->hasMany(EduSemestr::className(), ['course_id' => 'id']);
    }

    public function getTimeTables()
    {
        return $this->hasMany(TimeTable::className(), ['course_id' => 'id']);
    }

    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
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
