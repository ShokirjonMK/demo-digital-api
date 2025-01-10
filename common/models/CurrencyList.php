<?php

namespace common\models;

/**
 * This is the model class for table "currency_list".
 *
 * @property int $id
 * @property string|null $currency_name
 * @property string|null $currency_code
 * @property int $sort
 * @property int $status
 */
class CurrencyList extends \base\libs\RedisDB
{
    /**
     * Table name
     *
     * @return void
     */
    public static function tableName()
    {
        return 'currency_list';
    }

    /**
     * Set rules
     *
     * @return void
     */
    public function rules()
    {
        return [
            [['currency_name', 'currency_code'], 'string', 'max' => 50],
            [['sort', 'status'], 'integer'],
        ];
    }

    /**
     * Labels
     *
     * @return void
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency_name' => _e('Currency name'),
            'currency_code' => _e('Currency code'),
            'status' => _e('Status'),
            'sort' => _e('Sort'),
        ];
    }

    /**
     * Redis db relationship
     *
     * @return mixed
     */
    public function redisDbRelationship() {
        return array(new CurrencyRates());
    }

    /**
     * Get currency name
     *
     * @param string $currency_code
     * @return mixed
     */
    public static function getCurrencyName($currency_code)
    {
        $array = array(
            'CNY' => _e('CZECH KORUNA'),
            'CZK' => _e('JAPANESE YUAN'),
            'EUR' => _e('EURO'),
            'GBP' => _e('POUND STERLING'),
            'JPY' => _e('CHINESE YEN'),
            'RUB' => _e('RUSSIAN RUBLE'),
            'KZT' => _e('KAZAKHSTAN TENGE'),
            'TRY' => _e('TURKISH LIRA'),
            'USD' => _e('DOLLAR'),
            'UZS' => _e('UZBEKISTAN SOM'),
        );

        return isset($array[$currency_code]) ? $array[$currency_code] : '';
    }
}
