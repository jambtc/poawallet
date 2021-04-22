<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Blockchains;

/**
 * BlockchainsSearch represents the model behind the search form of `app\models\Blockchains`.
 */
class BlockchainsSearch extends Blockchains
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'invoice_expiration'], 'integer'],
            [['denomination', 'smart_contract_address', 'decimals', 'chain_id', 'url_block_explorer', 'smart_contract_abi', 'smart_contract_bytecode', 'sealer_address', 'sealer_private_key'], 'safe'],
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
        $query = Blockchains::find();

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
            'invoice_expiration' => $this->invoice_expiration,
            'decimals' => $this->decimals,
        ]);

        $query->andFilterWhere(['like', 'denomination', $this->denomination])
            ->andFilterWhere(['like', 'smart_contract_address', $this->smart_contract_address])
            ->andFilterWhere(['like', 'chain_id', $this->chain_id])
            ->andFilterWhere(['like', 'url_block_explorer', $this->url_block_explorer])
            ->andFilterWhere(['like', 'smart_contract_abi', $this->smart_contract_abi])
            ->andFilterWhere(['like', 'smart_contract_bytecode', $this->smart_contract_bytecode])
            ->andFilterWhere(['like', 'sealer_address', $this->sealer_address])
            ->andFilterWhere(['like', 'sealer_private_key', $this->sealer_private_key]);

        return $dataProvider;
    }
}
