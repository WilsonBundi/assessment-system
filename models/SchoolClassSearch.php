<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SchoolClass;

/**
 * SchoolClassSearch represents the model behind the search form of `app\models\SchoolClass`.
 */
class SchoolClassSearch extends SchoolClass
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_id', 'school_id'], 'integer'],
            [['class_name'], 'safe'],
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
        $query = SchoolClass::find();

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
            'class_id' => $this->class_id,
            'school_id' => $this->school_id,
        ]);

        $query->andFilterWhere(['ilike', 'class_name', $this->class_name]);

        return $dataProvider;
    }
}
