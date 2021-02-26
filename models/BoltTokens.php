<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bolt_tokens".
 *
 * @property int $id_token
 * @property int $id_user
 * @property string|null $type
 * @property string $status
 * @property float $token_price
 * @property float $token_ricevuti
 * @property float $fiat_price
 * @property string $currency
 * @property string $item_desc
 * @property string $item_code
 * @property int $invoice_timestamp
 * @property int $expiration_timestamp
 * @property float $rate
 * @property string $from_address
 * @property string $to_address
 * @property float $blocknumber
 * @property string $txhash
 */
class BoltTokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bolt_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'token_price', 'token_ricevuti', 'fiat_price', 'currency', 'item_desc', 'item_code', 'invoice_timestamp', 'expiration_timestamp', 'rate', 'from_address', 'to_address', 'blocknumber', 'txhash'], 'required'],
            [['id_user', 'invoice_timestamp', 'expiration_timestamp'], 'integer'],
            [['token_price', 'token_ricevuti', 'fiat_price', 'rate', 'blocknumber'], 'number'],
            [['type', 'status', 'from_address', 'to_address'], 'string', 'max' => 250],
            [['currency'], 'string', 'max' => 10],
            [['item_desc', 'item_code'], 'string', 'max' => 60],
            [['txhash'], 'string', 'max' => 80],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_token' => Yii::t('app', 'Id Token'),
            'id_user' => Yii::t('app', 'Id User'),
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'token_price' => Yii::t('app', 'Token Price'),
            'token_ricevuti' => Yii::t('app', 'Token Ricevuti'),
            'fiat_price' => Yii::t('app', 'Fiat Price'),
            'currency' => Yii::t('app', 'Currency'),
            'item_desc' => Yii::t('app', 'Item Desc'),
            'item_code' => Yii::t('app', 'Item Code'),
            'invoice_timestamp' => Yii::t('app', 'Invoice Timestamp'),
            'expiration_timestamp' => Yii::t('app', 'Expiration Timestamp'),
            'rate' => Yii::t('app', 'Rate'),
            'from_address' => Yii::t('app', 'From Address'),
            'to_address' => Yii::t('app', 'To Address'),
            'blocknumber' => Yii::t('app', 'Blocknumber'),
            'txhash' => Yii::t('app', 'Txhash'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\BoltTokensQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\BoltTokensQuery(get_called_class());
    }
}
