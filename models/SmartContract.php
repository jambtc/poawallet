<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "smart_contract".
 *
 * @property int $id
 * @property string $denomination
 * @property string $smart_contract_address
 * @property int $decimals
 * @property string $symbol
 *
 * @property Nodes[] $nodes
 */
class SmartContract extends \yii\db\ActiveRecord
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
            [['denomination', 'smart_contract_address', 'decimals', 'symbol'], 'required'],
            [['decimals'], 'integer'],
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
     * {@inheritdoc}
     * @return \app\models\query\SmartContractQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\SmartContractQuery(get_called_class());
    }
}
