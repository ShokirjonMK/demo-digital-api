<?php

namespace common\models\model;

class ElectionPass extends Election
{

    public function fields()
    {
        $fields =  [
            'id',
            'name' => function ($model) {
                return $model->translate->name ?? '';
            },
            'start',
            'finish',
            'role',
            'password',
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

            'description',

            'electionCondidate',
            'electionCondidateCount',
            'electionVoteCount',
            'electionVoteAll',
            'electionVote',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }
}
