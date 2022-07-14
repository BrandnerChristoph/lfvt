<?php

namespace app\models\search;

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
            [['id', 'classname', 'department', 'info'], 'safe'],
            [['period', 'class_head', 'studentsnumber', 'updated_at', 'created_at'], 'integer'],
            [['annual_value'], 'number'],
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
        $query = SchoolClass::find();

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
            'period' => $this->period,
            'annual_value' => $this->annual_value,
            'class_head' => $this->class_head,
            'studentsnumber' => $this->studentsnumber,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'classname', $this->classname])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }
}
