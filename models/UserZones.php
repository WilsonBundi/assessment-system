<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_zones".
 *
 * @property int $id
 * @property int $user_id
 * @property int $zone_id
 *
 * @property Users $user
 * @property Zone $zone
 */
class UserZones extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_zones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'zone_id'], 'required'],
            [['user_id', 'zone_id'], 'default', 'value' => null],
            [['user_id', 'zone_id'], 'integer'],
            [['user_id', 'zone_id'], 'unique', 'targetAttribute' => ['user_id', 'zone_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'user_id']],
            [['zone_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['zone_id' => 'zone_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'zone_id' => 'Zone ID',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['user_id' => 'user_id']);
    }

    /**
     * Gets query for [[Zone]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['zone_id' => 'zone_id']);
    }

}
