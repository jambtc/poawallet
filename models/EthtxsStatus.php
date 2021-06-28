<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ethtxs_status".
 *
 * @property int $id
 * @property int $id_blockchain
 * @property string $blocknumber
 *
 * @property Blockchains $blockchain
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
            [['id_blockchain', 'blocknumber'], 'required'],
            [['id_blockchain'], 'integer'],
            [['blocknumber'], 'string', 'max' => 50],
            [['id_blockchain'], 'exist', 'skipOnError' => true, 'targetClass' => Blockchains::className(), 'targetAttribute' => ['id_blockchain' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'id_blockchain' => Yii::t('app', 'Id Blockchain'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
        ];
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
     * {@inheritdoc}
     * @return \app\models\query\EthtxsStatusQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\EthtxsStatusQuery(get_called_class());
    }
}
