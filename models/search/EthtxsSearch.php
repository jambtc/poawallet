<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Ethtxs;

/**
 * EthtxsSearch represents the model behind the search form of `app\models\Ethtxs`.
 */
class EthtxsSearch extends Ethtxs
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'timestamp'], 'integer'],
            [['txfrom', 'txto', 'gas', 'gasprice', 'blocknumber', 'txhash', 'contract_to'], 'safe'],
            [['value', 'contract_value'], 'number'],
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
        $query = Ethtxs::find();

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
            'timestamp' => $this->timestamp,
            'value' => $this->value,
            'contract_value' => $this->contract_value,
        ]);

        $query->andFilterWhere(['like', 'txfrom', $this->txfrom])
            ->andFilterWhere(['like', 'txto', $this->txto])
            ->andFilterWhere(['like', 'gas', $this->gas])
            ->andFilterWhere(['like', 'gasprice', $this->gasprice])
            ->andFilterWhere(['like', 'blocknumber', $this->blocknumber])
            ->andFilterWhere(['like', 'txhash', $this->txhash])
            ->andFilterWhere(['like', 'contract_to', $this->contract_to]);

        return $dataProvider;
    }
}
