<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "erc20".
 *
 * @property int $id
 * @property string $smart_contract_abi
 * @property string $smart_contract_bytecode
 */
class Erc20abi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'erc20';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['smart_contract_abi', 'smart_contract_bytecode'], 'required'],
            [['smart_contract_abi', 'smart_contract_bytecode'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'smart_contract_abi' => Yii::t('app', 'Smart Contract Abi'),
            'smart_contract_bytecode' => Yii::t('app', 'Smart Contract Bytecode'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\Erc20abiQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\Erc20abiQuery(get_called_class());
    }
}
