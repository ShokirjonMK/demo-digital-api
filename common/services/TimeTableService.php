<?php

namespace common\services;

use common\models\model\EduSemestr;
use common\models\model\EduYear;
use common\models\model\Faculty;
use common\models\model\Kafedra;
use common\models\model\Student;
use common\models\model\Subject;
use common\models\model\TimeTable;
use Yii;


class TimeTableService
{
    public static function getCurrentStudent()
    {
        return Yii::$app->cache->getOrSet('student_user_' . current_user_id(), function () {
            return Student::findOne(['user_id' => current_user_id()]);
        }, 3600);
    }

    public static function getActiveEduYearIds()
    {
        return Yii::$app->cache->getOrSet('edu_year_active_ids', function () {
            return EduYear::find()->where(['status' => 1])->select('id')->column();
        }, 86400);
    }


    public static function getSemestersByPlan($edu_plan_id)
    {
        return Yii::$app->cache->getOrSet('edu_semester_plan_' . $edu_plan_id, function () use ($edu_plan_id) {
            return EduSemestr::find()->where(['edu_plan_id' => $edu_plan_id])->select('id')->column();
        }, 3600);
    }

    public static function getSubjectIdsByKafedra($kafedra_id)
    {
        return Yii::$app->cache->getOrSet('kafedra_' . $kafedra_id . '_subjects', function () use ($kafedra_id) {
            return Subject::find()->where(['kafedra_id' => $kafedra_id])->select('id')->column();
        }, 3600);
    }

    public static function getKafedraIdsByFaculty($faculty_id)
    {
        return Yii::$app->cache->getOrSet('faculty_' . $faculty_id . '_kafedras', function () use ($faculty_id) {
            return Kafedra::find()->where(['faculty_id' => $faculty_id])->select('id')->column();
        }, 86400);
    }

    public static function getSubjectIdsByKafedraIds($kafedra_ids)
    {
        $key = 'kafedra_' . implode('_', $kafedra_ids) . '_subjects';
        return Yii::$app->redis->getOrSet($key, function () use ($kafedra_ids) {
            return Subject::find()->where(['in', 'kafedra_id' => $kafedra_ids])->select('id')->column();
        }, 3600);
    }

    public static function getSubjectIdsByKafedraIds1($kafedra_ids)
    {
        return Subject::find()->where(['in', 'kafedra_id' => $kafedra_ids])->select('id')->column();
    }

    public static function getTimetableData($params, $lang, $currentUserId)
    {
        $model = new TimeTable();
        $query = $model->find()->andWhere(['is_deleted' => 0]);

        $student = self::getCurrentStudent();
        $archived = $params['archived'] ?? null;
        $this_year = $params['this_year'] ?? null;
        $subject_category_ids = $params['subject_category_ids'] ?? [];

        if ($this_year) {
            $query->andWhere(['in', 'edu_year_id', self::getActiveEduYearIds()]);
        } elseif ($archived) {
            $query->andWhere(['archived' => 1]);
        } else {
            $query->andWhere(['archived' => 0]);
        }

        if (isRole('student') && $student) {
            $query->andWhere(['in', 'edu_semester_id', self::getSemestersByPlan($student->edu_plan_id)]);
            $query->andWhere(['language_id' => $student->edu_lang_id]);
        } elseif (isRole('teacher') && !isRole('mudir') && !isRole('dean') && !isRole('edu_quality') && !isRole('time_table')) {
            $query->andFilterWhere(['teacher_user_id' => $currentUserId]);
        }

        if (isset($params['self']) && $params['self'] == 1) {
            $query->andFilterWhere(['teacher_user_id' => $currentUserId]);
        }

        if (!empty($subject_category_ids) && is_array($subject_category_ids)) {
            $query->andFilterWhere(['in', 'subject_category_id', $subject_category_ids]);
        }

        if (isset($params['kafedra_id'])) {
            $subjectIds = self::getSubjectIdsByKafedra($params['kafedra_id']);
            $query->andFilterWhere(['in', 'subject_id', $subjectIds]);
        }

        if (isset($params['faculty_id'])) {
            $kafedraIds = self::getKafedraIdsByFaculty($params['faculty_id']);
            $subjectIds = self::getSubjectIdsByKafedraIds($kafedraIds);
            $query->andFilterWhere(['in', 'subject_id', $subjectIds]);
        }

        if (isRole('dean')) {
            $t = Yii::$app->controller->isSelf(Faculty::USER_ACCESS_TYPE_ID);
            if ($t['status'] == 1) {
                $query->andWhere([$model->tableName() . '.faculty_id' => $t['UserAccess']->table_id]);
            } elseif ($t['status'] == 2) {
                $query->andFilterWhere([$model->tableName() . '.faculty_id' => -1]);
            }
        }

        // Qo‘shimcha metodlar controllerdan olinadi
        $query = Yii::$app->controller->filterAll($query, $model);
        $query = Yii::$app->controller->sort($query);

        return Yii::$app->controller->getData($query);
    }
}
