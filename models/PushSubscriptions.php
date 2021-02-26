<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bolt_vapid_subscription".
 *
 * @property int $id_subscription
 * @property int $id_user
 * @property string $type
 * @property string $browser
 * @property string $endpoint
 * @property string $auth
 * @property string $p256dh
 */
class PushSubscriptions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mp_subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'type', 'browser', 'endpoint', 'auth', 'p256dh'], 'required'],
            [['id_user'], 'integer'],
            [['type'], 'string', 'max' => 20],
            [['browser', 'endpoint', 'auth', 'p256dh'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_subscription' => Yii::t('app', 'Id Subscription'),
            'id_user' => Yii::t('app', 'Id User'),
            'type' => Yii::t('app', 'Type'),
            'browser' => Yii::t('app', 'Browser'),
            'endpoint' => Yii::t('app', 'Endpoint'),
            'auth' => Yii::t('app', 'Auth'),
            'p256dh' => Yii::t('app', 'P256dh'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\PushSubscriptionsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\PushSubscriptionsQuery(get_called_class());
    }
}
