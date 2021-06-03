<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SmartContract;

/**
 * SmartContractSearch represents the model behind the search form of `app\models\SmartContract`.
 */
class SmartContractsSearch extends SmartContract
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', ], 'integer'],
            [['denomination', 'url', 'symbol', 'chain_id', 'url_block_explorer'], 'safe'],
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
        $query = SmartContract::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'denomination', $this->denomination])
            ->andFilterWhere(['like', 'smart_contract_address', $this->smart_contract_address])
            ->andFilterWhere(['like', 'decimals', $this->decimals])
            ->andFilterWhere(['like', 'symbol', $this->symbol]);

        return $dataProvider;
    }
}
