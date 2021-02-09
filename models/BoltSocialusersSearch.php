<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BoltSocialusers;

/**
 * BoltSocialusersSearch represents the model behind the search form of `app\models\BoltSocialusers`.
 */
class BoltSocialusersSearch extends BoltSocialusers
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_social', 'id_user'], 'integer'],
            [['oauth_provider', 'oauth_uid', 'first_name', 'last_name', 'username', 'email', 'picture'], 'safe'],
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
        $query = BoltSocialusers::find();

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
            'id_social' => $this->id_social,
            'id_user' => $this->id_user,
        ]);

        $query->andFilterWhere(['like', 'oauth_provider', $this->oauth_provider])
            ->andFilterWhere(['like', 'oauth_uid', $this->oauth_uid])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'picture', $this->picture]);

        return $dataProvider;
    }
}
