<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transactions".
 *
 * @property int $id
 * @property int $id_user
 * @property string $type
 * @property int|null $id_smart_contract
 * @property string $status
 * @property float $token_price
 * @property float $token_received
 * @property int $invoice_timestamp
 * @property int $expiration_timestamp
 * @property string $from_address
 * @property string $to_address
 * @property string $blocknumber
 * @property string|null $txhash
 * @property string|null $message
 *
 * @property SmartContract $smartContract
 * @property Users $user
 */
class Transactions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'type', 'status', 'token_price', 'token_received', 'invoice_timestamp', 'expiration_timestamp', 'from_address', 'to_address', 'blocknumber'], 'required'],
            [['id_user', 'id_smart_contract', 'invoice_timestamp', 'expiration_timestamp'], 'integer'],
            [['token_price', 'token_received'], 'number'],
            [['type', 'status'], 'string', 'max' => 20],
            [['from_address', 'to_address'], 'string', 'max' => 100],
            [['blocknumber'], 'string', 'max' => 50],
            [['txhash'], 'string', 'max' => 250],
            [['message'], 'string', 'max' => 1000],
            [['id_smart_contract'], 'exist', 'skipOnError' => true, 'targetClass' => SmartContract::className(), 'targetAttribute' => ['id_smart_contract' => 'id']],
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
            'type' => Yii::t('app', 'Type'),
            'id_smart_contract' => Yii::t('app', 'Smart Contract'),
            'status' => Yii::t('app', 'Status'),
            'token_price' => Yii::t('app', 'Amount'),
            'token_received' => Yii::t('app', 'Received'),
            'invoice_timestamp' => Yii::t('app', 'Invoice Timestamp'),
            'expiration_timestamp' => Yii::t('app', 'Expiration Timestamp'),
            'from_address' => Yii::t('app', 'From Address'),
            'to_address' => Yii::t('app', 'To Address'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
            'txhash' => Yii::t('app', 'tx Hash'),
            'message' => Yii::t('app', 'Message'),
        ];
    }

    /**
     * Gets query for [[SmartContract]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\SmartContractQuery
     */
    public function getSmartContract()
    {
        return $this->hasOne(SmartContract::className(), ['id' => 'id_smart_contract']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\MpUsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(MpUsers::className(), ['id' => 'id_user']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\TransactionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\TransactionsQuery(get_called_class());
    }
}
