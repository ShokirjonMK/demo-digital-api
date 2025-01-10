<?php

namespace common\models;

/**
 * This is the model class for table "currency_rates".
 *
 * @property int $id
 * @property string|null $ckey
 * @property string|null $cname
 * @property string|null $cfrom
 * @property float|null $cto
 * @property float|null $cvalue
 * @property float|null $cvbefore
 * @property string|null $update_on
 */
class CurrencyRates extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'currency_rates';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['cto', 'cvalue'], 'number'],
            [['update_on'], 'safe'],
            [['ckey', 'cname', 'cfrom', 'cvbefore'], 'string', 'max' => 100],
        ];
    }

    /**
     * Labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ckey' => _e('Currency'),
            'cname' => _e('Name'),
            'cfrom' => _e('From'),
            'cto' => _e('To'),
            'cvalue' =>  _e('Price'),
            'cvbefore' => _e('Price before'),
            'update_on' =>  _e('Updated on'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new CurrencyList());
    }
    
}
