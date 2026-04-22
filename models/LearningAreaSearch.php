<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\LearningArea;

/**
 * LearningAreaSearch represents the model behind the search form of `app\models\LearningArea`.
 */
class LearningAreaSearch extends LearningArea
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['learning_area_id'], 'integer'],
            [['learning_area_name'], 'safe'],
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
        $query = LearningArea::find();

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
            'learning_area_id' => $this->learning_area_id,
        ]);

        $query->andFilterWhere(['ilike', 'learning_area_name', $this->learning_area_name]);

        return $dataProvider;
    }
}
