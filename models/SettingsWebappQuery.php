<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SettingsWebapp]].
 *
 * @see SettingsWebapp
 */
class SettingsWebappQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return SettingsWebapp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return SettingsWebapp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
