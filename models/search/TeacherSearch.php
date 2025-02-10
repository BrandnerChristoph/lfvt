<?php

namespace app\models\search;

use Yii;
use yii\base\Model;
use app\models\Teacher;
use app\models\TeacherFav;
use yii\data\ActiveDataProvider;
use mdm\admin\models\User;

/**
 * TeacherSearch represents the model behind the search form of `app\models\Teacher`.
 */
class TeacherSearch extends Teacher
{

    public $teacherListPreset;
    public $teacherListFavorites;
    
    public $sortOrder;
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at'], 'integer'],
            [['initial', 'name', 'firstname', 'email_1', 'email_2', 'phone', 'mobile', 'teacherListPreset', 'is_active', 'sortOrder'], 'safe'],
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
        $query = Teacher::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=> ['name' => SORT_ASC, 'firstname' => SORT_ASC, 'initial' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'is_active' => $this->is_active,
            'updated_at' => $this->updated_at,
            //'initial'=> $this->initial,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'initial', $this->initial])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'email_1', $this->email_1])
            ->andFilterWhere(['like', 'email_2', $this->email_2])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'mobile', $this->mobile]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchWithPreset($params)
    {   
        $query = Teacher::find();
        
        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder'=> ['name' => SORT_ASC, 'firstname' => SORT_ASC, 'initial' => SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $objUser = User::findOne(Yii::$app->user->id);
        $teacherListFavorites = TeacherFav::find()
                                    ->andFilterWhere(['type' => "teacher_myFavorites"])                                
                                    ->andFilterWhere(['user_id' => $objUser->username])
                                    ->All();
        $favItems = array();
        foreach($teacherListFavorites as $item)
            $favItems[]=$item->value;

        //$ids = implode('", "', array("BN", "SL"));
        $ids = implode('", "', $favItems);
        $query->addSelect((['*', 'CASE WHEN initial in ("'.$ids.'") then 1 else 0 END as sortOrder']));

        

        $query->andFilterWhere(['initial' => $this->teacherListFavorites]);
        $query->andFilterWhere(['initial' => $this->teacherListPreset]);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'is_active' => $this->is_active,
            'updated_at' => $this->updated_at,
            //'initial'=> $this->initial,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'initial', $this->initial])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'email_1', $this->email_1])
            ->andFilterWhere(['like', 'email_2', $this->email_2])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'mobile', $this->mobile]);

        //$query->orderBy('sortOrder desc, name asc');

        return $dataProvider;
    }
}
