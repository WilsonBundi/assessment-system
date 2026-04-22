<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "substrand".
 *
 * @property int $substrand_id
 * @property int $strand_id
 * @property string $name
 *
 * @property Strand $strand
 */
class Substrand extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'substrand';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['strand_id', 'name'], 'required'],
            [['strand_id'], 'default', 'value' => null],
            [['strand_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['strand_id'], 'exist', 'skipOnError' => true, 'targetClass' => Strand::class, 'targetAttribute' => ['strand_id' => 'strand_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'substrand_id' => 'Substrand ID',
            'strand_id' => 'Strand ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Strand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStrand()
    {
        return $this->hasOne(Strand::class, ['strand_id' => 'strand_id']);
    }

}
