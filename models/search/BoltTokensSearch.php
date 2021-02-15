<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BoltTokens;

/**
 * BoltTokensSearch represents the model behind the search form of `app\models\BoltTokens`.
 */
class BoltTokensSearch extends BoltTokens
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_token', 'id_user', 'invoice_timestamp', 'expiration_timestamp'], 'integer'],
            [['type', 'status', 'currency', 'item_desc', 'item_code', 'from_address', 'to_address', 'txhash'], 'safe'],
            [['token_price', 'token_ricevuti', 'fiat_price', 'rate', 'blocknumber'], 'number'],
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
        $query = BoltTokens::find();

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
            'id_token' => $this->id_token,
            'id_user' => $this->id_user,
            'token_price' => $this->token_price,
            'token_ricevuti' => $this->token_ricevuti,
            'fiat_price' => $this->fiat_price,
            'invoice_timestamp' => $this->invoice_timestamp,
            'expiration_timestamp' => $this->expiration_timestamp,
            'rate' => $this->rate,
            'blocknumber' => $this->blocknumber,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'currency', $this->currency])
            ->andFilterWhere(['like', 'item_desc', $this->item_desc])
            ->andFilterWhere(['like', 'item_code', $this->item_code])
            ->andFilterWhere(['like', 'from_address', $this->from_address])
            ->andFilterWhere(['like', 'to_address', $this->to_address])
            ->andFilterWhere(['like', 'txhash', $this->txhash]);

        return $dataProvider;
    }
}
