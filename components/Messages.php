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
use app\components\WebApp;

use yii\data\ActiveDataProvider;
use app\models\PushSubscriptions;

/*
* With this class I intend manage all notifications of app.
* Whether the user has to receive a push message, and store it in DB
*/


class Messages extends Component
{
    public static function save($attributes){
        $id_user = $attributes['id_user'];
        unset($attributes['id_user']);

        $model = new Notifications;
        $model->attributes = $attributes;
        $model->insert();
        // self::log("Salvato in notification ora chiamo readers...");

        // Aggiorna le notifiche da leggere per l'utente
        self::saveReader($id_user,$model->id);
        return (object) $model->attributes;
    }

    private function saveReader($id_user,$id_notification){
        $readers = new NotificationsReaders;
        $readers->id_user = $id_user;
        $readers->id_notification = $id_notification;
        $readers->alreadyread = NotificationsReaders::STATUS_UNREAD;
        $readers->insert();

        // self::log("Salvato in readers db");

        return true;
    }

    //scrive nel file log le informazioni richieste
    private function log($text){
        $logFileName = Yii::$app->basePath."/logs/messages-push.log";
        $handlefile = fopen($logFileName, "a");

		$time = "\r\n" .date('Y/m/d h:i:s a - ', time());
		fwrite($handlefile, $time.$text);
    }

    /**
    * FUNZIONE CHE INVIA UN MESSAGGIO PUSH
    *
    * @param $notification (array contenente la notifica)
    * @param $app (applicazione che riceverà la notifica) di default è Napay
    */
    public function push($attributes, $app='wallet')
    {
        // self::log("Salvo il messaggio in db");
        $notification = self::save($attributes);

        //Carico i parametri della webapp
        $settings = Settings::Vapid();

        // $subscriptions = array();
        // self::log("settings: <pre>".print_r($settings,true)."</pre>\n");

        $dataProvider = new ActiveDataProvider([
            'query' => PushSubscriptions::find()->
                where([
                    'id_user'=>$attributes['id_user'],
                    'type' => $app
                    ]),
            'pagination' => false // !!! IMPORTANT TO GET ALL MODELS
        ]);

        $content = [];

        // self::log("dataprovider: <pre>".print_r($dataProvider,true)."</pre>\n");
        // self::log("total count: <pre>".print_r($dataProvider->getTotalCount(),true)."</pre>\n");


        if ($dataProvider->getTotalCount() >0 )
        {
            // self::log("Dataprovider > 0\n");


            $auth = array(
                'VAPID' => array(
                    'subject' => $notification->description,
                    'publicKey' => $settings->public_key, // don't forget that your public key also lives in app.js
                    'privateKey' => WebApp::decrypt($settings->secret_key), // in the real world, this would be in a secret file
                ),
            );

            // self::log("auth: <pre>".print_r($auth,true)."</pre>\n");


            // impostazioni di default
            $defaultOptions = [
                'TTL' => 64800, //4 settimane
            ];

            // il contenuto del messaggio
            $content = array(
                'title' => '['.self::appTitle($notification->type,$app).'] - '. Yii::t('app','New message'), //'$array->type_notification,
                'body' => $notification->description,
                'icon' => 'src/images/icons/app-icon-96x96.png',
                'badge' => 'src/images/icons/app-icon-96x96.png',
                'image' => 'css/images/logo.png',
                'sound' => 'src/sounds/notify.mp3',
                'vibrate' => [200, 100, 200, 100, 200],
                'tag' => 'notify',
                'renotify' => true,
                'data' => [
                    'priority'=>'high',
                    'openUrl' => $notification->url,
                ],
                'actions' => [
                  ['action'=> 'openUrl', 'title'=> Yii::t('app','Yes'), 'icon'=> 'css/images/chk_on.png'],
                  ['action'=> 'close', 'title'=> Yii::t('app','No'), 'icon'=> 'css/images/chk_off.png'],
                    // ['action' => ['action'=>'open_url', 'action_url' => $array->url],
                    // 'title' => 'Apri link',
                    // ]
                ],

            );
            // self::log("content: <pre>".print_r($content,true)."</pre>\n");


            // self::log("Contenuto del messaggio è: <pre>".print_r($content,true)."</pre>\n");

            #echo '<pre>'.print_r($content,true).'</pre>';
            // trasformo il payload in json
            $payload = Json::encode($content);
            #echo '<pre>'.print_r($pay_load,true).'</pre>';
            // self::log("payload: <pre>".print_r($payload,true)."</pre>\n");


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
                // self::log("subscriptions: <pre>".print_r($subscriptions,true)."</pre>\n");

                #echo '<pre>'.print_r($array,true).'</pre>';
                #echo '<pre>'.print_r($subscription,true).'</pre>';
                // self::log("Invio messaggio per ciascuna subscription id_user:$item->id_user id_subscription:$item->id_subscription\n");


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
                    // self::log("subscription: <pre>".print_r($subscription,true)."</pre>\n");

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
              // // self::log(" : dataprovider = 0\n");
              // self::log("dataprovider = 0");

          }

          return $content;




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
                    $return = 'Poa Wallet';
                else
                    $return = 'Poa Wallet';

                break;

            default:
                $return = 'Fidelity';
                break;
        }
        return $return;
    }



}
