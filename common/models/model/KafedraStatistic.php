<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use api\resources\User;
use Yii;
use yii\behaviors\TimestampBehavior;


class KafedraStatistic extends Kafedra
{
    public static $selected_language = 'uz';


    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            // 'questionsCount' => function ($model) {
            //     return $model->questionsCount ?? 0;
            // },
            // 'questionCount',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [

            'direction',
            'leader',
            'userAccess',

            'faculty',
            'subjects',
            'description',


            'subjectsCount',
            'questions',
            'questionsCount',

            'teachers',
            'teachersCount',



            'description',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public function getQuestions()
    {
        return Question::find()
            // ->select('id')
            ->where([
                'in', 'subject_id',
                Subject::find()->where(['kafedra_id' => $this->id, 'is_deleted' => 0])->select('id')
            ])
            ->andWhere(['archived' => 0])
            ->all();
    }


    public function getQuestionsCount()
    {
        return count($this->questions);
    }

    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['kafedra_id' => 'id'])->onCondition(['is_deleted' => 0]);
    }

    public function getSubjectsCount()
    {
        return count($this->subjects);
    }

    public function getTeachers()
    {
        $model = new User();
        $query = $model->find()
            ->join(
                'INNER JOIN',
                'auth_assignment',
                'auth_assignment.user_id = users.id'
            )
            ->join(
                'INNER JOIN',
                'user_access',
                'user_access.user_id = users.id'
            );

        $query = $query->andWhere(['user_access.user_access_type_id' => self::USER_ACCESS_TYPE_ID]);
        $query = $query->andWhere(['user_access.table_id' => $this->id]);
        $query = $query->andWhere(['in', 'auth_assignment.item_name', ['teacher', 'mudir']]);
        return $query->all();
    }

    public function getTeachersCount()
    {
        return count($this->teachers);
    }
}
