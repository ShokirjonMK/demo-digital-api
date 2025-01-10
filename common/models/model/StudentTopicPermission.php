<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "faculty".
 *
 * @property int $id
 * @property int $student_id
 * @property int $user_id
 * @property int $attempts_count
 * @property string $topic_id
 * @property int|null $status
 * @property int $is_deleted
 */
class StudentTopicPermission extends \yii\db\ActiveRecord
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
        return 'student_topic_permission';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['user_id','topic_id'] , 'required'
            ],
            [['user_id','student_id','topic_id','attempts_count','status','created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['student_id'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['student_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['topic_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubjectTopic::className(), 'targetAttribute' => ['topic_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
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
            'student_id',
            'user_id',
            'topic_id',
            'attempts_count',
            'status',
        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'student',
            'topic'
        ];

        return $extraFields;
    }

    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }

    public static function updateItem($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!isset($post['topic_id'])) {
            $errors[] = [ 'topic_id' => _e("Subject Topic Id required") ];
        }
        if (!isset($post['start_time'])) {
            $errors[] = [ 'start_time' => _e("Start Time required") ];
        }
        if (!isset($post['end_time'])) {
            $errors[] = [ 'end_time' => _e("End Time required") ];
        }

        if (count($errors) != 0) {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        $subjectTopic = SubjectTopic::findOne([
            'id' => $post['topic_id'],
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!isset($subjectTopic)) {
            $errors[] = [ 'subject_topic_id' => _e("Subject Topic not faund") ];
            $transaction->rollBack();
            return simplify_errors($errors);
        }


        if (isRole('student')) {
            $secunt = $post['end_time'] - $post['start_time'];
            if ($secunt >= $subjectTopic->duration_reading_time) {
                $query = $model->findOne([
                    'user_id' => current_user_id(),
                    'topic_id' => $subjectTopic->id,
                    'status' => 0,
                    'is_deleted' => 0
                ]);
                if (isset($query)) {
                    $query->status = 1;
                    $query->save(false);
                } else {
                    $errors[] = [ 'content' => _e("You are not allowed to view the content") ];
                }
            } else {
                $errors[] = [ 'time' => _e("Content read timed out") ];
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
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}
