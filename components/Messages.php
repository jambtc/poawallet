<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Json;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use yii\base\Model;
use app\models\Notifications;
use app\models\NotificationsReaders;

use app\components\Settings;
use yii\data\ActiveDataProvider;
use app\models\PushSubscriptions;

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

    /**
    * FUNZIONE CHE INVIA UN MESSAGGIO PUSH
    *
    * @param $notification (array contenente la notifica)
    * @param $app (applicazione che riceverà la notifica) di default è Napay
    */
    public function push($attributes, $app='wallet')
    {

        $filename = Yii::$app->basePath."/web/assets/push-message.log";
		$myfile = fopen($filename, "a");

        fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Salvo il messaggio\n");
        $notification = self::save($attributes);

        //Carico i parametri della webapp
        $settings = Settings::load();

        // $subscriptions = array();
        fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Carico il dataProvider\n");

        $dataProvider = new ActiveDataProvider([
            'query' => PushSubscriptions::find()->
                where([
                    'id_user'=>$notification->id_user,
                    'type' => $app
                    ]),
            'pagination' => false // !!! IMPORTANT TO GET ALL MODELS
        ]);

        // $dataProvider = new ActiveDataProvider([
        //     'query' => PushSubscriptions::find()
        //                     ->andWhere('id_user' => $notification->id_user)
        //                     ->andWhere('type' => $app),
        //     'pagination' => false // !!! IMPORTANT TO GET ALL MODELS
        // ]);



        //
        // ) {
        //     $object['endpoint'] =  $item->endpoint;
        //     $object['auth'] =  $tabella->auth;
        //     $object['p256dh'] =  $tabella->p256dh;
        //   $subscriptions[$item->setting_name] = $item->setting_value;
        // }

        // $criteria=new CDbCriteria();
        // $criteria->compare('id_user',$array->id_user,false);
        // $criteria->compare('type',$app,false);

        #echo '<pre>'.print_r($criteria,true).'</pre>';

        // $subscribe_array = CHtml::listData(PushSubscriptions::model()->findAll($criteria), 'id_subscription', function($tabella) {
        //     $object['endpoint'] =  $tabella->endpoint;
        //     $object['auth'] =  $tabella->auth;
        //     $object['p256dh'] =  $tabella->p256dh;
        //     return $object;
        // });

        if ($dataProvider->getTotalCount() >0 )
        {
            fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : dataprovider > 0\n");


        // if (isset($subscribe_array)){
            // Crea autorizzazioni VAPID
            $auth = array(
                'VAPID' => array(
                    'subject' => $notification->description,
                    'publicKey' => $settings->VapidPublic, // don't forget that your public key also lives in app.js
                    'privateKey' => $settings->VapidSecret, // in the real world, this would be in a secret file
                ),
            );

            // impostazioni di default
            $defaultOptions = [
                'TTL' => 64800, //4 settimane
            ];

            // il contenuto del messaggio
            $content = array(
                'title' => '['.self::appTitle($notification->type_notification,$app).'] - '. Yii::t('lang','New message'), //'$array->type_notification,
                'body' => $notification->description,
                'icon' => 'src/images/icons/app-icon-96x96.png',
                'badge' => 'src/images/icons/app-icon-96x96.png',
                //'image' => $imagePath.'banner.png',
                //'sound' => $soundPath.'notification-sound.mp3',
                'vibrate' => [200, 100, 200, 100, 200],
                'tag' => 'notify',
                'renotify' => true,
                'data' => [
                    'priority'=>'high',
                    'openUrl' => $notification->url,
                ],
                'actions' => [
                  ['action'=> 'openUrl', 'title'=> Yii::t('lang','Yes'), 'icon'=> 'css/images/chk_on.png'],
                  ['action'=> 'close', 'title'=> Yii::t('lang','No'), 'icon'=> 'css/images/chk_off.png'],
                    // ['action' => ['action'=>'open_url', 'action_url' => $array->url],
                    // 'title' => 'Apri link',
                    // ]
                ],

            );

            fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Contenuto del messaggio è: <pre>".print_r($content,true)."</pre>\n");

            #echo '<pre>'.print_r($content,true).'</pre>';
            // trasformo il payload in json
            $payload = Json::encode($content);
            #echo '<pre>'.print_r($pay_load,true).'</pre>';

            // foreach ($subscribe_array as $id => $array){

            foreach ($dataProvider->getModels() as $item)
            {
                $subscriptions = Subscription::create([
                    "endpoint" => $item->endpoint,
                    "keys" => [
                        "p256dh" => $item->p256dh,
                        "auth" => $item->auth
                    ],
                ]);
                #echo '<pre>'.print_r($array,true).'</pre>';
                #echo '<pre>'.print_r($subscription,true).'</pre>';
                fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Invio messaggio per ciascuna subscription id_user:$item->id_user id_subscription:$item->id_subscription\n");


                // inizializzo la classe
                $webPush = new WebPush($auth, $defaultOptions);
                $webPush->setDefaultOptions($defaultOptions);

                /**
                 * send one notification and flush directly
                 * @var MessageSentReport $report
                 */
                foreach ($subscriptions as $subscription) {
                    $webPush->sendOneNotification(
                        $subscription,
                        $payload // optional (defaults null)
                    );
                }

                // // invio il messaggio push
                // $result = $webPush->sendNotification(
                //                 $subscription,
                //                 $payload,
                //                 true
                // );
            }

            /**
             * Check sent results
             * @var MessageSentReport $report
             */
              // if (isset($webPush)) {
              //     $save = new Save;
              //     foreach ($webPush->flush() as $report) {
              //         $endpoint = $report->getRequest()->getUri()->__toString();
              //
              //         if ($report->isSuccess()) {
              //             $save->WriteLog('libs','push','send',"Message sent successfully for subscription {$endpoint}.");
              //         } else {
              //             $save->WriteLog('libs','push','send',"[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
              //         }
              //     }
              // }
          } else {
              fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : dataprovider = 0\n");
          }
    }
    /**
    * Funzione che restituisce il titolo del messaggio push in base al type_notification
    **/
    private function appTitle($type_notification,$app){
        switch ($type_notification) {
            case 'invoice':
            case 'help':
            case 'fattura':
                $return = 'Fidelity';
                break;

            case 'token':
            case 'contact':
                if ($app == 'wallet')
                    $return = 'MegaPay';
                else
                    $return = 'MegaPay';

                break;

            default:
                $return = 'Fidelity';
                break;
        }
        return $return;
    }



}
