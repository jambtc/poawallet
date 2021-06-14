<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "smart_contract".
 *
 * @property int $id
 * @property int $id_user
 * @property int $id_blockchain
 * @property int $id_contract_type
 * @property string $denomination
 * @property string $smart_contract_address
 * @property int $decimals
 * @property string $symbol
 *
 * @property Nodes[] $nodes
 * @property Blockchains $blockchain
 * @property ContractType $contractType
 * @property Users $user
 */
class SmartContracts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'smart_contract';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_blockchain', 'id_contract_type', 'denomination', 'smart_contract_address', 'decimals', 'symbol'], 'required'],
            [['id_user', 'id_blockchain', 'id_contract_type', 'decimals'], 'integer'],
            [['denomination', 'smart_contract_address', 'symbol'], 'string', 'max' => 255],
            [['id_blockchain'], 'exist', 'skipOnError' => true, 'targetClass' => Blockchains::className(), 'targetAttribute' => ['id_blockchain' => 'id']],
            [['id_contract_type'], 'exist', 'skipOnError' => true, 'targetClass' => ContractType::className(), 'targetAttribute' => ['id_contract_type' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_user' => Yii::t('app', 'Id User'),
            'id_blockchain' => Yii::t('app', 'Network'),
            'id_contract_type' => Yii::t('app', 'Contract Type'),
            'denomination' => Yii::t('app', 'Denomination'),
            'smart_contract_address' => Yii::t('app', 'Smart Contract Address'),
            'decimals' => Yii::t('app', 'Decimals'),
            'symbol' => Yii::t('app', 'Symbol'),
        ];
    }

    /**
     * Gets query for [[Nodes]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\NodesQuery
     */
    public function getNodes()
    {
        return $this->hasMany(Nodes::className(), ['id_smart_contract' => 'id']);
    }

    /**
     * Gets query for [[Blockchain]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\BlockchainsQuery
     */
    public function getBlockchain()
    {
        return $this->hasOne(Blockchains::className(), ['id' => 'id_blockchain']);
    }

    /**
     * Gets query for [[ContractType]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\ContractTypeQuery
     */
    public function getContractType()
    {
        return $this->hasOne(ContractType::className(), ['id' => 'id_contract_type']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MpUsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'id_user']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\SmartContractsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\SmartContractsQuery(get_called_class());
    }
}
