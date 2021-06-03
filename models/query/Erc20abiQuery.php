<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Erc20abi]].
 *
 * @see \app\models\Erc20abi
 */
class Erc20abiQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Erc20abi[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Erc20abi|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
