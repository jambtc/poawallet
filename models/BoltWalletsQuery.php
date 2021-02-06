<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BoltWallets]].
 *
 * @see BoltWallets
 */
class BoltWalletsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BoltWallets[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BoltWallets|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
