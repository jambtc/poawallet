<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_notifications".
 *
 * @property int $id_notification
 * @property string $type_notification
 * @property int $id_user
 * @property int $id_tocheck
 * @property string $status
 * @property string $description
 * @property string $url
 * @property int $timestamp
 * @property float $price
 * @property int $deleted
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_notification', 'id_user', 'id_tocheck', 'status', 'description', 'url', 'timestamp'], 'required'],
            [['id_user', 'id_tocheck', 'timestamp', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['type_notification', 'status', 'url'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_notification' => Yii::t('app', 'Id Notification'),
            'type_notification' => Yii::t('app', 'Type Notification'),
            'id_user' => Yii::t('app', 'Id User'),
            'id_tocheck' => Yii::t('app', 'Id Tocheck'),
            'status' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Description'),
            'url' => Yii::t('app', 'Url'),
            'timestamp' => Yii::t('app', 'Timestamp'),
            'price' => Yii::t('app', 'Price'),
            'deleted' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\NotificationsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\NotificationsQuery(get_called_class());
    }
}
