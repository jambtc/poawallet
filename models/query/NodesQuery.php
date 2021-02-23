<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[Nodes]].
 *
 * @see Nodes
 */
class NodesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Nodes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Nodes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
