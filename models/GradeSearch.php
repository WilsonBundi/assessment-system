<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Grade;

/**
 * GradeSearch represents the model behind the search form of `app\models\Grade`.
 */
class GradeSearch extends Grade
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['grade_id', 'assessment_id', 'competence_id', 'score'], 'integer'],
            [['level', 'remarks'], 'safe'],
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
        $query = Grade::find();

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
            'grade_id' => $this->grade_id,
            'assessment_id' => $this->assessment_id,
            'competence_id' => $this->competence_id,
            'score' => $this->score,
        ]);

        $query->andFilterWhere(['ilike', 'level', $this->level])
            ->andFilterWhere(['ilike', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
