<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Blockchains]].
 *
 * @see \app\models\Blockchains
 */
class BlockchainsQuery extends \yii\db\ActiveQuery
{
    public function byUserId($id)
    {
        return $this->andWhere([
            'id_user' => $id
        ]);
    }
    public function byChain($chain)
    {
        return $this->andWhere([
            'chain_id' => $chain
        ]);
    }
    public function bySymbol($symbol)
    {
        return $this->andWhere([
            'symbol' => $symbol
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Blockchains[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Blockchains|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
