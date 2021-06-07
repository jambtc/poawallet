<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "standard_blockchain_values".
 *
 * @property int $id
 * @property string $denomination
 * @property string $chain_id
 * @property string $url
 * @property string $symbol
 * @property string|null $url_block_explorer
 */
class StandardBlockchainValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'standard_blockchain_values';
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
     * {@inheritdoc}
     * @return \app\models\query\StandardBlockchainValuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\StandardBlockchainValuesQuery(get_called_class());
    }
}
