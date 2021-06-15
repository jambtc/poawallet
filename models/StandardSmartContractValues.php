<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "standard_smart_contract_values".
 *
 * @property int $id
 * @property int $id_contract_type
 * @property string $denomination
 * @property string $smart_contract_address
 * @property int $decimals
 * @property string $symbol
 */
class StandardSmartContractValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'standard_smart_contract_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_contract_type', 'denomination', 'smart_contract_address', 'decimals', 'symbol'], 'required'],
            [['id_contract_type', 'decimals'], 'integer'],
            [['denomination', 'smart_contract_address', 'symbol'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_contract_type' => Yii::t('app', 'Id Contract Type'),
            'denomination' => Yii::t('app', 'Denomination'),
            'smart_contract_address' => Yii::t('app', 'Smart Contract Address'),
            'decimals' => Yii::t('app', 'Decimals'),
            'symbol' => Yii::t('app', 'Symbol'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\StandardSmartContractValuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\StandardSmartContractValuesQuery(get_called_class());
    }
}
