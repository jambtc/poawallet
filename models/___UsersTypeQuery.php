<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[UsersType]].
 *
 * @see UsersType
 */
class UsersTypeQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UsersType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UsersType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
