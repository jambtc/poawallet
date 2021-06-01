<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "blockchains".
 *
 * @property int $id
 * @property string $denomination
 * @property string $chain_id
 * @property string $url
 * @property string $symbol
 * @property string|null $url_block_explorer
 *
 * @property Nodes[] $nodes
 */
class Blockchains extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'blockchains';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['denomination', 'chain_id', 'url', 'symbol'], 'required'],
            [['denomination', 'url', 'symbol', 'url_block_explorer'], 'string', 'max' => 255],
            [['chain_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'denomination' => Yii::t('app', 'Denomination'),
            'chain_id' => Yii::t('app', 'Chain ID'),
            'url' => Yii::t('app', 'Url'),
            'symbol' => Yii::t('app', 'Symbol'),
            'url_block_explorer' => Yii::t('app', 'Url Block Explorer'),
        ];
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\NodesQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Nodes::className(), ['id_blockchain' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\BlockchainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlockchainsQuery(get_called_class());
    }
}
