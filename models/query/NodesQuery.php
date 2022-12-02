<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\Nodes]].
 *
 * @see \app\models\Nodes
 */
class NodesQuery extends \yii\db\ActiveQuery
{
    public function byUserId($id)
    {
        return $this->andWhere([
            'id_user' => $id
        ]);
    }

   

    /**
     * {@inheritdoc}
     * @return \app\models\Nodes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\Nodes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
