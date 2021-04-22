<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mp_wallets".
 *
 * @property int $id
 * @property int|null $id_user
 * @property string $wallet_address
 * @property string $blocknumber
 *
 * @property Users $user
 */
class MPWallets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mp_wallets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user'], 'integer'],
            [['wallet_address', 'blocknumber'], 'required'],
            [['wallet_address', 'blocknumber'], 'string', 'max' => 50],
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
            'wallet_address' => Yii::t('app', 'Wallet Address'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
        ];
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
     * @return \app\models\query\MPWalletsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\MPWalletsQuery(get_called_class());
    }
}
