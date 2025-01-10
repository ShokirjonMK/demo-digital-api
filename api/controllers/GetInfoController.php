<?php

namespace api\controllers;

use aki\telegram\base\Command;
use common\models\model\Profile;
use common\models\model\Student;
use common\models\model\StudentGroup;
use common\models\model\StudentSemestrSubject;
use Yii;
use base\ResponseStatus;
use api\forms\Login;
use common\models\model\LoginHistory;
use common\models\model\StudentTimeTable;
use yii\httpclient\Client;

class GetInfoController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        unset($behaviors['permissionCheck']);
        unset($behaviors['authorCheck']);
        return $behaviors;
    }

    public function actionAcademikReference($key) {
        $model = StudentGroup::findOne([
            'semestr_key' => $key,
            'status' => 1,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $query = StudentGroup::find()
            ->select(['id' , 'student_id'])
            ->where(['student_id' => $model->student_id , 'status' => 1 , 'is_deleted' => 0])
            ->orderBy('semestr_id desc')
            ->one();

        $studentSemestrSubjects = StudentSemestrSubject::find()
            ->where(['student_id' => $query->student_id , 'status' => 1, 'is_deleted' => 0])
            ->all();
        $subjects = [];
        if (count($studentSemestrSubjects) > 0) {
            foreach ($studentSemestrSubjects as $studentSemestrSubject) {
                $eduSemestrSubject = $studentSemestrSubject->eduSemestrSubject;
                $subjects[] = [
                    'semestr_id' => $studentSemestrSubject->semestr_id,
                    'eduSemestrSubject' => [
                        'credit' => $eduSemestrSubject->credit,
                        'categoryAllHour' => $eduSemestrSubject->categoryAllHour,
                        'subject' => [
                            'name' => $eduSemestrSubject->subject->translate->name,
                        ]
                    ],
                    'all_ball' => $studentSemestrSubject->all_ball,
                    'rating' => rating($studentSemestrSubject->all_ball),
                ];
            }
        }

        $student = Student::findOne($query->student_id);
        $profile = Profile::findOne(['user_id' => $student->user_id]);
        $direction = $student->direction;
        $faculty = $student->faculty;
        $leader = $faculty->leader->profile;
        $eduPlan = $student->eduPlan;
        $eduForm = $student->eduForm;
        $eduYear = $eduPlan->eduYear;
        $eduYear = $eduYear->start_year. " - ". $eduYear->end_year . " - ". $eduYear->type;
        $data = [
            'first_name' => $profile->first_name,
            'last_name' => $profile->last_name,
            'middle_name' => $profile->middle_name,
            'direction' => $direction->translate->name,
            'eduPlan' => $eduPlan->translate->name,
            'eduForm' => $eduForm->translate->name,
            'eduYear' => $eduYear,
            'faculty' => [
                'id' => $faculty->id,
                'name' => $faculty->translate->name,
                'leader' => [
                    'first_name' => $leader->first_name,
                    'last_name' => $leader->last_name,
                    'middle_name' => $leader->middle_name,
                ]
            ],
            'subjects' => $subjects
        ];
        return $this->response(1, _e('Successfully get data.'), $data, null, ResponseStatus::OK);
    }
}