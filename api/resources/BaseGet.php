<?php

namespace api\resources;

use common\models\model\EduSemestr;
use common\models\model\EduSemestrSubjectCategoryTime;
use common\models\model\Group;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentGroup as CommonStudentGroup;
use common\models\model\StudentMark;
use common\models\model\StudentSemestrSubject;
use common\models\model\StudentSemestrSubjectVedomst;
use common\models\model\TimetableDate;
use common\models\SubjectInfo;
use Yii;

class BaseGet
{
    public static function studentCount($filter)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->join('INNER JOIN', 'users', 'users.id = student.user_id')
            ->groupBy('profile.user_id');

        $count =  $query->andWhere($filter)->count();
        return (int)$count;
    }

    public static function studentGet($filter)
    {
        $model = new Student();

        $query = $model->find()
            ->with(['profile'])
            ->where(['student.is_deleted' => 0])
            ->andWhere($filter)
            ->join('INNER JOIN', 'profile', 'profile.user_id = student.user_id')
            ->join('INNER JOIN', 'users', 'users.id = student.user_id')
            ->groupBy('profile.user_id')
            ->all();

        return $query;
    }

}
