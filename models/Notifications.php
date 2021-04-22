<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications".
 *
 * @property int $id
 * @property int $timestamp
 * @property string $type
 * @property string $status
 * @property string $description
 * @property string $url
 * @property float $price
 *
 * @property NotificationsReaders[] $notificationsReaders
 */
class Notifications extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mp_notifications';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['timestamp', 'type', 'status', 'description', 'url', 'price'], 'required'],
            [['timestamp'], 'integer'],
            [['description', 'url'], 'string'],
            [['price'], 'number'],
            [['type', 'status'], 'string', 'max' => 50],
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
            'type' => Yii::t('app', 'Type'),
            'status' => Yii::t('app', 'Status'),
            'description' => Yii::t('app', 'Description'),
            'url' => Yii::t('app', 'Url'),
            'price' => Yii::t('app', 'Price'),
        ];
    }

    /**
     * Gets query for [[NotificationsReaders]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\NotificationsReadersQuery
     */
    public function getNotificationsReaders()
    {
        return $this->hasMany(NotificationsReaders::className(), ['id_notification' => 'id']);
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
