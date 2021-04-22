<?php

namespace app\models;

use Yii;
use app\components\WebApp;

/**
 * This is the model class for table "blockchains".
 *
 * @property int $id
 * @property string $denomination
 * @property int $invoice_expiration
 * @property string $smart_contract_address
 * @property string $chain_id
 * @property string $url_block_explorer
 * @property string $smart_contract_abi
 * @property string $smart_contract_bytecode
 * @property string $sealer_address
 * @property string $sealer_private_key

 * @property Nodes[] $nodes
 * @property Stores[] $stores
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
            [['denomination', 'invoice_expiration', 'smart_contract_address', 'chain_id', 'url_block_explorer', 'smart_contract_abi', 'smart_contract_bytecode', 'sealer_address', 'sealer_private_key'], 'required'],
            [['invoice_expiration','decimals'], 'integer'],
            [['denomination', 'smart_contract_address', 'url_block_explorer', 'smart_contract_abi', 'smart_contract_bytecode', 'sealer_address', 'sealer_private_key'], 'string', 'max' => 255],
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
            'denomination' => Yii::t('app', 'Blockchain Denomination'),
            'invoice_expiration' => Yii::t('app', 'Invoice Expiration'),
            'smart_contract_address' => Yii::t('app', 'Smart Contract Address'),
            'decimals' => Yii::t('app', 'Decimals'),
            'chain_id' => Yii::t('app', 'Chain ID'),
            'url_block_explorer' => Yii::t('app', 'Url Block Explorer'),
            'smart_contract_abi' => Yii::t('app', 'Smart Contract Abi'),
            'smart_contract_bytecode' => Yii::t('app', 'Smart Contract Bytecode'),
            'sealer_address' => Yii::t('app', 'Sealer Address'),
            'sealer_private_key' => Yii::t('app', 'Sealer Private Key'),
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
     * Gets query for [[Stores]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\StoresQuery
     */
    public function getStores()
    {
        return $this->hasMany(Stores::className(), ['id_blockchain' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\BlockchainsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BlockchainsQuery(get_called_class());
    }

    public function beforeSave($insert) {
        $this->sealer_private_key = WebApp::encrypt($this->sealer_private_key);

        return parent::beforeSave($insert);
    }
}
