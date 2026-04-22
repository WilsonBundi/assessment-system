<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Assessment;

/**
 * AssessmentSearch represents the model behind the search form of `app\models\Assessment`.
 */
class AssessmentSearch extends Assessment
{
    public $zone_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['assessment_id', 'examiner_user_id', 'school_id', 'learning_area_id', 'total_score', 'zone_id', 'validated_by'], 'integer'],
            [['student_reg_no', 'assessment_date', 'start_time', 'end_time', 'overall_level'], 'safe'],
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
        $query = Assessment::find()->joinWith('school');

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
            'assessment.assessment_id' => $this->assessment_id,
            'assessment.examiner_user_id' => $this->examiner_user_id,
            'assessment.school_id' => $this->school_id,
            'assessment.learning_area_id' => $this->learning_area_id,
            'assessment.assessment_date' => $this->assessment_date,
            'assessment.start_time' => $this->start_time,
            'assessment.end_time' => $this->end_time,
            'assessment.total_score' => $this->total_score,
            'assessment.validated_by' => $this->validated_by,
        ]);

        $query->andFilterWhere(['school.zone_id' => $this->zone_id]);

        $query->andFilterWhere(['ilike', 'assessment.student_reg_no', $this->student_reg_no])
            ->andFilterWhere(['ilike', 'assessment.overall_level', $this->overall_level]);

        return $dataProvider;
    }
}
