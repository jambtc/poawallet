<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Transactions]].
 *
 * @see \app\models\Transactions
 */
class TransactionsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\Transactions[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Transactions|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function findByHash($hash){
        return $this->andWhere(['txhash'=>$hash])->one();
    }
}
