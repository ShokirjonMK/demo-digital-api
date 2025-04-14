<?php

namespace api\controllers;

use Yii;
use base\ResponseStatus;
use common\models\enums\DisabilityGroup;
use common\models\enums\Education;
use common\models\enums\EducationDegree;
use common\models\enums\EducationType;
use common\models\enums\FamilyStatus;
use common\models\enums\Gender;
use common\models\enums\Rates;
use common\models\enums\TopicType;
use common\models\enums\YesNo;

class EnumController extends ApiController
{
    public function actionGenders()
    {
        return $this->responceData(Gender::list());
    }

    public function actionRates()
    {
        return $this->responceData(Rates::list());
    }

    public function actionDisabilityGroups()
    {
        return $this->responceData(DisabilityGroup::list());
    }

    public function actionEducations()
    {
        return $this->responceData(Education::list());
    }

    public function actionEducationDegrees()
    {
        return $this->responceData(EducationDegree::list());
    }

    public function actionEducationTypes()
    {
        return $this->responceData(EducationType::list());
    }

    public function actionFamilyStatuses()
    {
        return $this->responceData(FamilyStatus::list());
    }

    public function actionYesno()
    {
        return $this->responceData(YesNo::list());
    }

    public function actionTopicTypes()
    {
        return $this->responceData(TopicType::list());
    }

    private function responceData($data)
    {
        return $this->response(1, _e('Success.'), $data, null, ResponseStatus::OK);
    }
}
