<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;

use yii\base\Model;
use app\models\Notifications;
use app\models\NotificationsReaders;

/*
* With this class I intend manage all notifications of app.
* Whether the user has to receive a push message, and store it in DB
*/


class Messages extends Component
{
    public static function save($attributes){
        $model = new Notifications;

        $model->attributes = $attributes;
        $model->id_user = $attributes['id_user'];
        $model->insert();

        // Aggiorna le notifiche da leggere per l'utente
        self::saveReader($model->id_user,$model->id_notification);
        return (object) $model->attributes;
    }

    private function saveReader($id_user,$id_notification){
        $readers = new NotificationsReaders;
        $readers->id_user = $id_user;
        $readers->id_notification = $id_notification;
        $readers->alreadyread = NotificationsReaders::STATUS_UNREAD;
        $readers->insert();

        return true;
    }

}
