<?php

namespace common\models\model;

class Chichqoq extends \yii\db\ActiveRecord
{


    public static function tableName()
    {
        return 'chichqoq';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [
                [
                    'a1',
                    'a2',
                    'a4',
                    'a5',
                    'a6',

                ], 'integer'
            ],

            [['a3'], 'string'],


        ];
    }
}
