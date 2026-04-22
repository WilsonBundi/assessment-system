<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StudentSupervisorAssignment;

/**
 * StudentSupervisorAssignmentSearch represents the model behind the search form of `app\models\StudentSupervisorAssignment`.
 */
class StudentSupervisorAssignmentSearch extends StudentSupervisorAssignment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['assignment_id', 'supervisor_user_id', 'school_id', 'assigned_by', 'zone_id'], 'integer'],
            [['student_reg_no', 'assigned_at', 'status', 'notes'], 'safe'],
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
        $query = StudentSupervisorAssignment::find();

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
            'assignment_id' => $this->assignment_id,
            'supervisor_user_id' => $this->supervisor_user_id,
            'school_id' => $this->school_id,
            'assigned_by' => $this->assigned_by,
            'assigned_at' => $this->assigned_at,
            'zone_id' => $this->zone_id,
        ]);

        $query->andFilterWhere(['ilike', 'student_reg_no', $this->student_reg_no])
            ->andFilterWhere(['ilike', 'status', $this->status])
            ->andFilterWhere(['ilike', 'notes', $this->notes]);

        return $dataProvider;
    }
}
