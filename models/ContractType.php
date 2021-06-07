<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contract_type".
 *
 * @property int $id
 * @property string $denomination
 * @property string $smart_contract_abi
 * @property string $smart_contract_bytecode
 *
 * @property SmartContract[] $smartContracts
 */
class ContractType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['denomination', 'smart_contract_abi', 'smart_contract_bytecode'], 'required'],
            [['smart_contract_abi', 'smart_contract_bytecode'], 'string'],
            [['denomination'], 'string', 'max' => 255],
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
            'smart_contract_abi' => Yii::t('app', 'Smart Contract Abi'),
            'smart_contract_bytecode' => Yii::t('app', 'Smart Contract Bytecode'),
        ];
    }

    /**
     * Gets query for [[SmartContracts]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\SmartContractQuery
     */
    public function getSmartContracts()
    {
        return $this->hasMany(SmartContract::className(), ['id_contract_type' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\ContractTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\ContractTypeQuery(get_called_class());
    }
}
