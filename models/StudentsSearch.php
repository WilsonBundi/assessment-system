<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Students;

/**
 * StudentsSearch represents the model behind the search form of `app\models\Students`.
 */
class StudentsSearch extends Students
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_id', 'school_id'], 'integer'],
            [['student_reg_no', 'surname', 'other_name', 'phone_no', 'email'], 'safe'],
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
        $query = Students::find();

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
            'student_id' => $this->student_id,
            'school_id' => $this->school_id,
        ]);

        $query->andFilterWhere(['ilike', 'student_reg_no', $this->student_reg_no])
            ->andFilterWhere(['ilike', 'surname', $this->surname])
            ->andFilterWhere(['ilike', 'other_name', $this->other_name])
            ->andFilterWhere(['ilike', 'phone_no', $this->phone_no])
            ->andFilterWhere(['ilike', 'email', $this->email]);

        return $dataProvider;
    }
}
