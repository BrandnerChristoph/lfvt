<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TeacherWishlist;

/**
 * TeacherWishlistSearch represents the model behind the search form of `app\models\TeacherWishlist`.
 */
class TeacherWishlistSearch extends TeacherWishlist
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'period', 'info'], 'safe'],
            [['teacher_id', 'updated_at', 'created_at'], 'integer'],
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
        $query = TeacherWishlist::find();

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
            'teacher_id' => $this->teacher_id,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'period', $this->period])
            ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }
}
