<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[BoltUsers]].
 *
 * @see BoltUsers
 */
class BoltUsersQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return BoltUsers[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return BoltUsers|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
