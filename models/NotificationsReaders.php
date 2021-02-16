<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "np_notifications_readers".
 *
 * @property int $id_notifications_reader
 * @property int $id_user
 * @property int $id_notification
 * @property int $alreadyread
 */
class NotificationsReaders extends \yii\db\ActiveRecord
{
    const STATUS_READ = 1;
    const STATUS_UNREAD = 0;

    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'np_notifications_readers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_notification'], 'required'],
            [['id_user', 'id_notification', 'alreadyread'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_notifications_reader' => Yii::t('app', 'Id Notifications Reader'),
            'id_user' => Yii::t('app', 'Id User'),
            'id_notification' => Yii::t('app', 'Id Notification'),
            'alreadyread' => Yii::t('app', 'Alreadyread'),
        ];
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\NotificationsReadersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\NotificationsReadersQuery(get_called_class());
    }
}
