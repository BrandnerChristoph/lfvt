<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\ClassSubject;

/**
 * ClassSubjectSearch represents the model behind the search form of `app\models\ClassSubject`.
 */
class ClassSubjectSearch extends ClassSubject
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'class', 'subject', 'group', 'classroom'], 'safe'],
            [['value'], 'number'],
            [['teacher', 'updated_at', 'created_at'], 'integer'],
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
        $query = ClassSubject::find();

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
            'value' => $this->value,
            'teacher' => $this->teacher,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'class', $this->class])
            ->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'group', $this->group])
            ->andFilterWhere(['like', 'classroom', $this->classroom]);

        return $dataProvider;
    }
}
