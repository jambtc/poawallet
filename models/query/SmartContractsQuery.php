<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\SmartContracts]].
 *
 * @see \app\models\SmartContracts
 */
class SmartContractsQuery extends \yii\db\ActiveQuery
{
    public function byUserId($id)
    {
        return $this->andWhere([
            'id_user' => $id
        ]);
    }

    public function byBlockchainId($id)
    {
        return $this->andWhere([
            'id_blockchain' => $id
        ]);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\SmartContracts[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\SmartContracts|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
