<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\httpclient\Client;

use app\models\Nodes;


class WebApp extends Component
{
    /**
     * Recupera casualmente il primo nodo POA disponibile
     * @return nodeurl
     */
    public static function getPoaNode()
    {
        $nodelist = ArrayHelper::map(Nodes::find()->all(), 'id_node', function ($item, $defaultValue) {
            return $item->url . ':' . $item->port;
        });

        $isdown = true;
        shuffle($nodelist);
        do {
            $node = array_shift($nodelist);

            if (empty($node)){
                return false;
            }

            if( self::checkUrl( $node ) ) {
                $isdown = false;
            }
        } while ($isdown);

        return $node;
    }

    public static function checkUrl($url) {
        $client = new Client();

        $request = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setOptions([
                'timeout' => 1, // set timeout to 1 seconds for the case server is not responding
            ])
            ->send();

        return $request->getisOk();
    }

    /**
     * funzione che crypta un testo
     * @param $text il testo da criptare
     * @return testo criptato
     */

    public static function encrypt($data) {
        return strtr(self::enc($data,  hash( 'sha256', self::secretFromFile('secret_key') )), '', '');
    }

    public static function decrypt($data) {
        return strtr(self::dec($data,  hash( 'sha256', self::secretFromFile('secret_key') )), '', '');
    }

    /**
     * Metodi privati della classe
     */
    private static function enc($data, $key) {
        return base64_encode( openssl_encrypt( $data, self::secretFromFile('encrypt_method'), $key, 0, substr( hash( 'sha256', self::secretFromFile('secret_iv') ), 0, 16 ) ) );
    }

    private static function dec($data, $key) {
        return openssl_decrypt( base64_decode( $data ), self::secretFromFile('encrypt_method'), $key, 0, substr( hash( 'sha256', self::secretFromFile('secret_iv') ), 0, 16 ) );
    }

    /**
     * questa funzione prova a caricare il secret_key e secret_iv dalla directory corrente in un file
     * @param $key la chiave da leggere
     * @return restituisce la chiave letta
     */
    private static function secretFromFile($key)
    {
        $file = Yii::$app->params['encryptionFile'];

        if (file_exists($file) === false) {
            echo "Unable to load config from: " . $file . PHP_EOL;
            echo "Detected no SECRET KEY or SECRET IV, all signed requests will fail" . PHP_EOL;
            die;
        }
        $contents = Json::decode(file_get_contents($file));

        return isset($contents[$key]) ? $contents[$key] : "";
    }

    public static function showTransactionRow($data, $fromAddress){
        $dateLN = date("d M `y",$data->invoice_timestamp);
        $timeLN = date("H:i:s",$data->invoice_timestamp);

        if ($data->from_address == $fromAddress){
          $price = '- '.$data->token_price;
          $color = 'red';
          $addressToShow = $data->to_address;
        } else {
          $price = $data->token_price;
          $color = 'green';
          $addressToShow = $data->from_address;
        }
        $coinImg = ($data->type == 'token') ? 'coin5' : 'coin2';

        $classStatus = ($data->status == 'complete') ? 'bg-success' : 'bg-secondary';

        $line = '
        <a href="'.Url::to(['tokens/view', 'id' => self::encrypt($data->id_token)]).'" />
        <div class="container-fluid m-0 p-0">
              <div class="row">
                  <div class="col-12 m-0 p-0">
                      <div class="card shadow">
                          <div class="transaction-card-horizontal">
                              <div class="img-square-wrapper">
                                  <img class="img-xxs pl-1 pt-2" src="css/img/content/'.$coinImg.'.png" alt="coin image">
                              </div>
                              <div class="transaction-card-body ml-1">
                                  <h6 class="card-title pt-2"><small>'.$data->id_token."</small> ".substr($addressToShow,0,21).'...</h6>
                                  <p class="card-text">
                                  <small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
                                  </p>
                              </div>
                              <div class="card-footer">
                                  <b class="d-block mb-0 text-center txt-'.$color.'">'.$price.'</b>
                                  <small class="text-light text-capitalize text-center pl-2 pr-2 '.$classStatus.'" id="transaction-status-'
                                  .self::encrypt($data->id_token).'">'.$data->status.'</small>
                              </div>
                          </div>

                      </div>
                  </div>
              </div>
          </div>
          </a>
          ';
         return $line;
    }

    public static function Icon($type_notification,$type_transaction=null){
        switch (strtolower(trim($type_notification))){
            //ricordati i case tutti minuscoli
            case 'help':
                $zmdicon = 'fa fa-question';
                break;

                case 'alarm':
                    $zmdicon = 'fa fa-exclamation';
                    break;


            // case 'fattura':
            //     $zmdicon = 'zmdi zmdi-collection-pdf';
            //     break;
            case 'invoice':
                $zmdicon = 'fab fa-btc';
                break;

            case 'token':
                $zmdicon = 'fa fa-star';
                break;

                case 'ether':
                    $zmdicon = 'fab fa-ethereum';
                    break;

            // case 'withdraw':
            //     $zmdicon = 'zmdi zmdi-balance';
            //     break;

            // case 'deposit':
            //     $zmdicon = 'fa fa-eur';
            //     break;

            case  'contact':
              $zmdicon = 'fa fa-users';
              break;


            case 'new':
            case 'complete':
            // case 'paid':
            // case 'confirmed':
            // case 'invoice':
              $zmdicon = 'fa fa-star';
                // $zmdicon = 'fab fa-btc';
                break;


            // case 'expired':
            // case 'invalid':
            // case 'failed':
            //     $zmdicon = 'fa fa-exclamation';
            //     break;

            default:
                $zmdicon = 'fa fa-info';
        }
        return $zmdicon;
    }
    public static function Color($status){
        switch (strtolower(trim($status))){
            //ricordati i case tutti minuscoli
            case 'alarm':
                $color = 'bg-danger';
                break;

            case 'help':
                $color = 'bg-success';
                break;

            case 'fattura':
            case 'sending':
            case 'new':
                $color = 'bg-dark';
                break;

            case 'failed':
            case 'invalid':
                $color = 'bg-danger';
                break;

            case 'expired':
            case 'paidpartial':
                $color = 'bg-warning';
                break;

            case 'complete':
            case 'paid':
            case 'confirmed':
            case 'sent':
                $color = 'bg-success';
                break;

            case 'paidover':
                $color = 'bg-primary';
                break;

          case 'followed':
              $color = "bg-success";
              break;

          case 'unfollowed':
                  $color = "bg-warning";
                  break;

            default:
                $color = 'bg-secondary';
        }
        return $color;
    }

    /**
	* Questa funziona mostra a video il tipo di transazione e se è stata inviata o ricevuta
	*/
	public function typePrice($price,$sent){
		return ($sent == 'sent' ? '<h5 class="text-warning">-' : '<h5 class="text-success">+') . $price . '</h5>';
	}

    public static function showMessagesRow($data){
        $dateLN = date("d M `y",$data->timestamp);
        $timeLN = date("H:i:s",$data->timestamp);

        $notifi__icon = self::Icon(
            (strpos($data->description,'token') !== false ? 'token' : $data->type_notification )
        );

        switch ($data->status){
            case 'complete':
                $classStatus = 'bg-success';
                break;
            case 'expired':
                $classStatus = 'bg-warning';
                break;

            default:
                $classStatus = 'bg-secondary';
        }

        // $classStatus = ($data->status == 'complete') ? 'bg-success' : ($data->status == 'expired') ? 'bg-warning' : 'bg-secondary';

        $line = '
        <a href="'.Url::to(['tokens/view', 'id' => self::encrypt($data->id_tocheck)]).'" />
        <div class="container-fluid m-0 p-0">
              <div class="row">
                  <div class="col-12 m-0 p-0">
                      <div class="card shadow">
                          <div class="transaction-card-horizontal">
                          <div class=" d-flex align-items-center">
                              <div class="rounded-circle bg-success p-2 ml-1">
                                 <i class="'.$notifi__icon.' text-light" style="font-size:1.5em;"></i>
                              </div>
                              <div class="transaction-card-body ml-1">
                                  <h6 class="card-title pt-2 text-break">'.$data->description.'</h6>
                                  <p class="card-text">
                                  <small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
                                  </p>
                              </div>
                              <div class="card-footer">
                                  <b class="d-block mb-0 text-center txt-green">'.$data->price.'</b>
                                  <small class="text-light text-capitalize text-center pl-2 pr-2 '.$classStatus.'" >'
                                    .$data->status.'</small>
                              </div>
                          </div>
                         </div>
                      </div>
                  </div>
              </div>
          </div>
          </a>
          ';
         return $line;
    }
}
