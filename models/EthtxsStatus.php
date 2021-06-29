<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ethtxs_status".
 *
 * @property int $id
 * @property string $symbol
 * @property string $blocknumber
 */
class EthtxsStatus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ethtxs_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'blocknumber'], 'required'],
            [['symbol'], 'string', 'max' => 255],
            [['blocknumber'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'symbol' => Yii::t('app', 'Symbol'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\EthtxsStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\EthtxsStatusQuery(get_called_class());
    }
}
