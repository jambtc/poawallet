<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\helpers\Json;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

use app\components\Settings;
use yii\data\ActiveDataProvider;
use app\models\PushSubscriptions;


class Push extends Component
{
    /**
    * FUNZIONE CHE INVIA UN MESSAGGIO PUSH
    *
    * @param $notification (array contenente la notifica)
    * @param $app (applicazione che riceverà la notifica) di default è Napay
    */
    public function send($notification, $app='dashboard')
    {
        //Carico i parametri della webapp
        $settings = Settings::load();

        // $subscriptions = array();
        $dataProvider = new ActiveDataProvider([
            'query' => PushSubscriptions::find()
                            ->andWhere('id_user' => $notification->id_user)
                            ->andWhere('type' => $app),
            'pagination' => false // !!! IMPORTANT TO GET ALL MODELS
        ]);



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
                'title' => '['.self::appTitle($notification->type_notification,$app).'] - '. Yii::t('app','New message'), //'$array->type_notification,
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
                  ['action'=> 'openUrl', 'title'=> Yii::t('app','Yes'), 'icon'=> 'css/images/chk_on.png'],
                  ['action'=> 'close', 'title'=> Yii::t('app','No'), 'icon'=> 'css/images/chk_off.png'],
                    // ['action' => ['action'=>'open_url', 'action_url' => $array->url],
                    // 'title' => 'Apri link',
                    // ]
                ],

            );
            #echo '<pre>'.print_r($content,true).'</pre>';
            // trasformo il payload in json
            $payload = Json::encode($content);
            #echo '<pre>'.print_r($pay_load,true).'</pre>';

            // foreach ($subscribe_array as $id => $array){

            foreach ($dataProvider->getModels() as $item)
            {
                $subscription = Subscription::create([
                    "endpoint" => $item->endpoint,
                    "keys" => [
                        "p256dh" => $item->p256dh,
                        "auth" => $item->auth
                    ],
                ]);
                #echo '<pre>'.print_r($array,true).'</pre>';
                #echo '<pre>'.print_r($subscription,true).'</pre>';

                // inizializzo la classe
                $webPush = new WebPush($auth, $defaultOptions);
                $webPush->setDefaultOptions($defaultOptions);

                // invio il messaggio push
                $result = $webPush->sendNotification(
                                $subscription,
                                $payload,
                                true
                );
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
                    $return = 'Wallet';
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
