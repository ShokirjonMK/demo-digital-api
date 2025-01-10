<?php

namespace api\forms;

use yii\base\Model;

class EmployeeSubject extends Model
{
    
    public $user_id;
    public $subject_id;
    public $language_ids;

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['user_id', 'subject_id','language_ids'], 'required'],
            [['user_id', 'subject_id'], 'integer'],
            [['language_ids'], 'string'],
        ];
    }

}