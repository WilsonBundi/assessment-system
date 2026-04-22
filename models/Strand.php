<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "strand".
 *
 * @property int $strand_id
 * @property int $learning_area_id
 * @property string $name
 *
 * @property LearningArea $learningArea
 * @property Substrand[] $substrands
 */
class Strand extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'strand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['learning_area_id', 'name'], 'required'],
            [['learning_area_id'], 'default', 'value' => null],
            [['learning_area_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['learning_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => LearningArea::class, 'targetAttribute' => ['learning_area_id' => 'learning_area_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'strand_id' => 'Strand ID',
            'learning_area_id' => 'Learning Area ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[LearningArea]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLearningArea()
    {
        return $this->hasOne(LearningArea::class, ['learning_area_id' => 'learning_area_id']);
    }

    /**
     * Gets query for [[Substrands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubstrands()
    {
        return $this->hasMany(Substrand::class, ['strand_id' => 'strand_id']);
    }

}
