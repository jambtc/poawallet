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
use app\models\Blockchains;
use app\models\Notifications;

class WebApp extends Component
{
    // /**
    //  * Recupera casualmente il primo nodo POA disponibile
    //  * @return nodeurl
    //  */
    // public static function getPoaNode($blockchain_id = 1)
    // {
    //     $blockchain = Blockchains::findOne($blockchain_id);
    //     // $nodes = $blockchain->nodes;
    //     //
    //     // $nodelist = ArrayHelper::map(
    //     //     $nodes,
    //     //     'id_node',
    //     //     function ($item, $defaultValue) {
    //     //         return $item->url . ':' . $item->port;
    //     //     }
    //     // );
    //     //
    //     // $isdown = true;
    //     // shuffle($nodelist);
    //     // do {
    //     //     $node = array_shift($nodelist);
    //     //
    //     //     if (empty($node)){
    //     //         return false;
    //     //     }
    //     //
    //     //     if( self::checkUrl( $node ) ) {
    //     //         $isdown = false;
    //     //     }
    //     // } while ($isdown);
    //
    //     // return $node;
    //
    //     if( self::checkUrl( $blockchain->url ) ) {
    //         return false;
    //     }
    //     // echo $blockchain->url;
    //     // exit;
    //     return $blockchain->url;
    // }
    /**
	 * PHP/cURL function to check a web site status. If HTTP status is not 200 or 302, or
	 * the requests takes longer than 1 seconds, the website is unreachable.
	 *
	 * Follow me on Twitter: @sergiocasizzone
	 *
	 * @param string $url URL that must be checked
	 * @param integer $timeout Seconds to timeout
	 */
	function checkUrl( $url, $timeout = 1 ) {
		$ch = curl_init();

		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		$http_respond = curl_exec($ch);
		$http_respond = trim( strip_tags( $http_respond ) );
		$http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ( ( $http_code == "200" )
			|| ( $http_code == "201" )
			|| ( $http_code == "302" )
		) {
			return true;
		} else {
			return false;
		}
		curl_close( $ch );
	}

    // public static function checkUrl($url) {
    //     $client = new Client();
    //
    //     $request = $client->createRequest()
    //         ->setMethod('GET')
    //         ->setUrl($url)
    //         ->setOptions([
    //             'timeout' => 1, // set timeout to 1 seconds for the case server is not responding
    //         ])
    //         ->send();
    //
    //     return $request->getisOk();
    // }

	private function si_classifier($val){
		$suffixes = [
			24 => [
					'long_suffix' => 'yotta',
					'short_suffix' => 'Y',
					'scalar' => gmp_strval('1000000000000000000000000')
			],
			21 => [
					'long_suffix' => 'zetta',
					'short_suffix' => 'Z',
					'scalar' => gmp_strval('1000000000000000000000')
			],
			18 => [
					'long_suffix' => 'exa',
					'short_suffix' => 'E',
					'scalar' => pow(1000, 6)
			],
			15 => [
					'long_suffix' => 'peta',
					'short_suffix' => 'P',
					'scalar' => pow(1000, 5)
			],
			12 => [
					'long_suffix' => 'tera',
					'short_suffix' => 'T',
					'scalar' => pow(1000, 4)
			],
			9 => [
					'long_suffix' => 'giga',
					'short_suffix' => 'G',
					'scalar' => pow(1000, 3)
			],
			6 => [
					'long_suffix' => 'mega',
					'short_suffix' => 'M',
					'scalar' => pow(1000, 2)
			],
			3 => [
					'long_suffix' => 'kilo',
					'short_suffix' => 'k',
					'scalar' => pow(1000, 1)
			],
			0 => [
					'long_suffix' => '',
					'short_suffix' => '',
					'scalar' => pow(1000, 0)
			],
			-3 => [
					'long_suffix' => 'milli',
					'short_suffix' => 'm',
					'scalar' => bcmul('1', '0.001', 10)
			],
			-6 => [
					'long_suffix' => 'micro',
					'short_suffix' => 'µ',
					'scalar' => bcmul('1', '0.000001', 10)
			],
			-9 => [
					'long_suffix' => 'nano',
					'short_suffix' => 'n',
					'scalar' => bcmul('1', '0.000000001', 10)
			],
			-12 => [
					'long_suffix' => 'pico',
					'short_suffix' => 'p',
					'scalar' => bcmul('1', '0.000000000001', 13)
			],
			-15 => [
					'long_suffix' => 'femto',
					'short_suffix' => 'n',
					'scalar' => bcmul('1', '0.000000000000001', 16)
			],
			-18 => [
					'long_suffix' => 'atto',
					'short_suffix' => 'a',
					'scalar' => bcmul('1', '0.000000000000000001', 19)
			],
			-21 => [
					'long_suffix' => 'zepto',
					'short_suffix' => 'z',
					'scalar' => bcmul('1', '0.000000000000000000001', 22)
			],
			-24 => [
					'long_suffix' => 'yopto',
					'short_suffix' => 'y',
					'scalar' => bcmul('1', '0.000000000000000000000001', 25)
			],

		];
		$exponent = intval(floor(log10(abs($val))/3.0)*3);
		return $suffixes[$exponent] ?? null;
	}


	// Best shortens a number and attaches K, M, B, etc. accordingly
	public function si_formatter($number, $style = 'short'){
		$classifier = self::si_classifier($number);
		if ($classifier === null){
			# Don't know how to classify this value
		    return $number;
		}
		$scaled = round($number / $classifier['scalar'], 3);

		if ($style == 'short'){
			return $scaled.' '.$classifier['short_suffix'];
		} else {
			return $scaled.' '.$classifier['long_suffix'];
		}

	}

	// Shortens a number and attaches K, M, B, etc. accordingly
    // public function number_shorten($number, $precision = 3, $divisors = null) {
    //     // Setup default $divisors if not provided
    //     if (!isset($divisors)) {
    //         $divisors = array(
	// 			pow(1000, -4) => 'p', // 0.000000000001 pico
	// 			pow(1000, -3) => 'n', // 0.000000001 nano
	// 			pow(1000, -2) => 'µ', // 0.000001 micro
	// 			pow(1000, -1) => 'm', // 0.001 milli
	// 			pow(1000, 0) => '', // 1000^0 == 1
    //             pow(1000, 1) => 'k', // Thousand
    //             pow(1000, 2) => 'M', //Yii::t('app','Million'), // Mega - Million
    //             pow(1000, 3) => 'G', //Yii::t('app','Billion'), // Giga - Billion
    //             pow(1000, 4) => 'T', //Yii::t('app','Trillion'), // Tera - Trillion
    //             pow(1000, 5) => 'P', //Yii::t('app','Quadrillion'), // Peta - Quadrillion
    //             pow(1000, 6) => 'E',// Yii::t('app','Quintillion'), // Exa - Quintillion
    //             gmp_strval('1000000000000000000000') => 'Z', //Yii::t('app','Sextillion'), // Sextillion
    //             gmp_strval('1000000000000000000000000') => 'Y', //Yii::t('app','Septillion'), // Septillion
    //         );
    //     }
	//
    //     // Loop through each $divisor and find the
    //     // lowest amount that matches
    //     foreach ($divisors as $divisor => $shorthand) {
    //         if (abs($number) < ($divisor * 1000)) {
    //             // We found a match!
    //             break;
    //         }
    //     }
    //     // We found our match, or there were no matches.
    //     // Either way, use the last defined value for $divisor.
    //     return round(number_format($number / $divisor, $precision),$precision) .' '. $shorthand;
    // }

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

    public static function showTransactionRow($data, $fromAddress,$newTransaction=false,$status=null){
        $dateLN = date("d M `y",$data->invoice_timestamp);
        $timeLN = date("H:i:s",$data->invoice_timestamp);

        if ($data->from_address == $fromAddress){
          $price = '- '.self::si_formatter($data->token_price);
          $color = 'red';
          $addressToShow = $data->to_address;
          $coinImg = 'fa-arrow-up text-danger';
          $recipient = Yii::t('app','To: ');
        } else {
          $price = self::si_formatter($data->token_price);
          $color = 'green';
          $addressToShow = $data->from_address;
          $coinImg = 'fa-arrow-down text-success';
          $recipient = Yii::t('app','From: ');
        }
        // $coinImg = ($data->type == 'token') ? 'coin5' : 'coin2';

        $classStatus = ($data->status == 'complete') ? 'bg-success' : 'bg-secondary';
        $newTxClass = ($newTransaction == true) ? 'bg-newtransaction' : '';

		if ($data->status == 'new' && $data->txhash != ''){
			$data->status = 'pending';
			$status = true;
		}

        $line = '
        <a href="index.php?r=transactions/view&id='. self::encrypt($data->id) . '" />
        <div class="container-fluid m-0 p-0">
              <div class="row d-flex justify-content-center">
                  <div class="col-11 m-0 p-0">
                      <div class="card shadow">
                          <div class="transaction-card-horizontal '.$newTxClass.'">
                              <div class="img-square-wrapper ">
                                  <i class="pt-2 px-1 fas fa-2x '.$coinImg.'"></i>
                              </div>
                              <div class="transaction-card-body ml-1 mb-0">
                                  <span class="card-title">
                                    <small class="pt-1 d-inline-block text-truncate" style="max-width: 150px;">'.$recipient.$addressToShow.'</small>
                                  </span>
                                  <p class="card-text">
                                  <small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
                                  </p>
                              </div>

                              <div class="alert p-1 m-2">
                                  <span class="mb-0 txt-'.$color.'">'.$price.'
									  <small class="ml-1">
									  	'.$data->smartContract->symbol.'
									  </small>
								  </span>
								  ';
		if ($status !== null){
			$line .= '			</br>
								<div class="d-flex justify-content-center">
								<small class="text-light text-capitalize text-center pl-2 pr-2 '.$classStatus.'" id="transaction-status-'
								.self::encrypt($data->id).'">'.$data->status.'</small>
								</div>
								';
		}
        $line .=             '</div>
                      </div>';

				          ($data->message != "")
                          ?  $line .= '<small class="mx-2 alert alert-info d-inline-block text-truncate" style="max-width: 330px;">'.$data->message.'</small>'
                          :  $line .= '';
                          $line .= '
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

    public static function showMessagesRow($item){
        $data = Notifications::findOne($item->id_notification);
        // return print_r($data,true);

        $dateLN = date("d M `y",$data->timestamp);
        $timeLN = date("H:i:s",$data->timestamp);

        $notifi__icon = self::Icon(
            (strpos($data->description,'token') !== false ? 'token' : $data->type )
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
        <a href="'.$data->url.'" />
        <div class="container-fluid m-0 p-0">
              <div class="row ml-0">
                  <div class="col-11 m-0 p-0">
                      <div class="card shadow">
                          <div class=" d-flex d-flex-grow">

                              <div class="transaction-card-body ml-1">
                                  <h6 class="card-title pt-2 text-break">'.$data->description.'</h6>

                              </div>


                          </div>
						  <p class="text-right card-text">
						  	<small class="mr-2 text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
						  </p>

                      </div>
                  </div>
              </div>
          </div>
          </a>
          ';
         return $line;
    }
}
