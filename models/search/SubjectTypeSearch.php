<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SubjectType;

/**
 * SubjectTypeSearch represents the model behind the search form of `app\models\SubjectType`.
 */
class SubjectTypeSearch extends SubjectType
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'info', 'year_update'], 'safe'],
            [['updated_at', 'created_at', 'sortorder'], 'integer'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SubjectType::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
            'sortorder' => $this->sortorder,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
                ->andFilterWhere(['like', 'name', $this->name])
                ->andFilterWhere(['like', 'year_update', $this->year_update])
                ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }
}
