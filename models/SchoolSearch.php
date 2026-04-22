<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\School;

/**
 * SchoolSearch represents the model behind the search form of `app\models\School`.
 */
class SchoolSearch extends School
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['school_id', 'zone_id'], 'integer'],
            [['school_code', 'school_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = School::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'school_id' => $this->school_id,
            'zone_id' => $this->zone_id,
        ]);

        $query->andFilterWhere(['ilike', 'school_code', $this->school_code])
            ->andFilterWhere(['ilike', 'school_name', $this->school_name]);

        return $dataProvider;
    }
}
