<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "notifications_readers".
 *
 * @property int $id
 * @property int $id_notification
 * @property int $id_user
 * @property int $alreadyread
 *
 * @property Notifications $notification
 * @property Users $user
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
        return 'mp_notifications_readers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_notification', 'id_user', 'alreadyread'], 'required'],
            [['id_notification', 'id_user', 'alreadyread'], 'integer'],
            [['id_notification'], 'exist', 'skipOnError' => true, 'targetClass' => Notifications::className(), 'targetAttribute' => ['id_notification' => 'id']],
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
            'id_notification' => Yii::t('app', 'Id Notification'),
            'id_user' => Yii::t('app', 'Id User'),
            'alreadyread' => Yii::t('app', 'Alreadyread'),
        ];
    }

    /**
     * Gets query for [[Notification]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\NotificationsQuery
     */
    public function getNotification()
    {
        return $this->hasOne(Notifications::className(), ['id' => 'id_notification']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|\app\models\query\UsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'id_user']);
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
