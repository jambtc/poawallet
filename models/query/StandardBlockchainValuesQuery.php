<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\StandardBlockchainValues]].
 *
 * @see \app\models\StandardBlockchainValues
 */
class StandardBlockchainValuesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \app\models\StandardBlockchainValues[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\StandardBlockchainValues|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
