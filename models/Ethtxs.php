<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ethtxs".
 *
 * @property int $id
 * @property int $timestamp
 * @property string $txfrom
 * @property string $txto
 * @property string $gas
 * @property string $gasprice
 * @property string $blocknumber
 * @property string|null $txhash
 * @property float $value
 * @property string|null $contract_to
 * @property float $contract_value
 */
class Ethtxs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ethtxs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp', 'txfrom', 'txto', 'gas', 'gasprice', 'blocknumber', 'value', 'contract_value'], 'required'],
            [['timestamp'], 'integer'],
            [['value', 'contract_value'], 'number'],
            [['txfrom', 'txto', 'contract_to'], 'string', 'max' => 100],
            [['gas', 'gasprice', 'blocknumber'], 'string', 'max' => 50],
            [['txhash'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'txfrom' => Yii::t('app', 'Txfrom'),
            'txto' => Yii::t('app', 'Txto'),
            'gas' => Yii::t('app', 'Gas'),
            'gasprice' => Yii::t('app', 'Gasprice'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
            'txhash' => Yii::t('app', 'Txhash'),
            'value' => Yii::t('app', 'Value'),
            'contract_to' => Yii::t('app', 'Contract To'),
            'contract_value' => Yii::t('app', 'Contract Value'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\EthtxsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\EthtxsQuery(get_called_class());
    }
}
