<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zone".
 *
 * @property int $zone_id
 * @property string $zone_name
 *
 * @property School[] $schools
 */
class Zone extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zone';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zone_name'], 'required'],
            [['zone_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'zone_id' => 'Zone ID',
            'zone_name' => 'Zone Name',
        ];
    }

    /**
     * Gets query for [[Schools]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSchools()
    {
        return $this->hasMany(School::class, ['zone_id' => 'zone_id']);
    }

}
