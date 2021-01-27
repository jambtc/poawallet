<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Users;

/**
 * UsersSearch represents the model behind the search form of `\app\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_users_type', 'status_activation_code'], 'integer'],
            [['email', 'password', 'ga_secret_key', 'name', 'surname', 'corporate', 'denomination', 'vat', 'address', 'cap', 'city', 'country', 'activation_code'], 'safe'],
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
        $query = Users::find();

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
            'id_user' => $this->id_user,
            'id_users_type' => $this->id_users_type,
            'status_activation_code' => $this->status_activation_code,
        ]);

        $query->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'ga_secret_key', $this->ga_secret_key])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'surname', $this->surname])
            ->andFilterWhere(['like', 'corporate', $this->corporate])
            ->andFilterWhere(['like', 'denomination', $this->denomination])
            ->andFilterWhere(['like', 'vat', $this->vat])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'cap', $this->cap])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'activation_code', $this->activation_code]);

        return $dataProvider;
    }
}
