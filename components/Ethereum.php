<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\web\Response;
use yii\web\HttpException;
use yii\helpers\Json;


use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

use Web3p\EthereumTx\Transaction;
use Nullix\CryptoJsAes\CryptoJsAes;

// use app\components\WebApp;


class Ethereum extends Component
{
    private $url;

    public $balance = 0; // token balance
    public $decimals = 0; // decimals into smart contract
    // public $noncevalue = 0; // nonce count
    public $blocknumber = 0; // blocknumber
    public $transaction = null;


    public function __construct($url) {
        $this->url = $url;
    }

    // public function __construct() {
    //     // $this->blockchain_id = $blockchain_id;
    // }

    private static function json ($data)
	{
		//Yii::$app->response->format = Response::FORMAT_JSON;
		return Json::encode($data);
	}

    private function setBalance($balance){
        $value = (string) $balance * 1;
        $this->balance = $value;
    }
    private function getBalance(){
        return $this->balance;
    }

    private function setTransaction($transaction){
        $this->transaction = $transaction;
    }
    private function getTransaction(){
        return $this->transaction;
    }
    // recupera lo streaming json dal contenuto txt del body
    private function getJsonBody($response)
    {
        $start = strpos($response,'{',0);
        $substr = substr($response,$start);
        return json_decode($substr, true);
    }






    // get the transactions number of address
    public function getNonce($address)
    {
        $web3 = new Web3(new HttpProvider(new HttpRequestManager($this->url)));

    	$response = null;
		$web3->eth->getTransactionCount($address, function ($err, $nonce) use (&$response) {
			if($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
            $response = gmp_intval($nonce->value);
		});

        // echo 'nonce info: <pre>'.print_r($response,true).'</pre>';
        // die();
        return $response;

    }


    public function gasBalance($address)
    {
        $web3 = new Web3(new HttpProvider(new HttpRequestManager($this->url)));

        //recupero il balance
        $balance = 0;
        $web3->eth->getBalance($address, function ($err, $response) use (&$balance){
            if ($err !== null) {
                $balance = 0;
            } else {
                $balance = $response->toString() ;

            }


            // echo 'response: <pre>'.print_r(gmp_strval($response->value),true).'</pre>';
            // exit;

        });
        $value = (string) $balance * 1;
		//$balance = $value / (1*pow(10,18)); //1000000000000000000;

        return $value;

    }



    public function loadGas($sealer_id,$to,$amount)
    {
        $web3 = new Web3(new HttpProvider(new HttpRequestManager($this->url)));

        $response = null;
        $sealer_account = Yii::$app->params['sealers'][$sealer_id]['address'];
        $prv_key = Yii::$app->params['sealers'][$sealer_id]['key'];

        // preparing items
        $fromAccount = $sealer_account; // sealer
        $toAccount = $to;
        $hex = dechex(21004);
        $gas = '0x'.$hex;

        // recupero la nonce per l'account
        $nonce = $this->getNonce($fromAccount);
        $transaction = new Transaction([
            'nonce' => '0x'.dechex($nonce), //è un object BigInteger
            'from' => $fromAccount, //indirizzo sealer Blockchain
            'to' => $toAccount, //indirizzo commerciante
            'gas' => '0x200b20', // gas necessario per la transazione
            'gasPrice' => '1000', // gasPrice giusto?
            'value' => $amount * pow(10, 18),
            'chainId' => 1,
            'data' =>  '0x0', // non ci sono dati per contratto
        ]);
        $transaction->offsetSet('chainId', 1);
        $signed_transaction = $transaction->sign($prv_key); //

        $web3->eth->sendRawTransaction(sprintf('0x%s', $signed_transaction), function ($err, $tx) use (&$response){
            if ($err !== null) {
                $response = [
                    'success' => 0,
                    'message' => $err->getMessage(),
                    'tx' => null,
                ];
            }else{
                $response = [
                    'success' => 1,
                    'message' => '',
                    'tx' => $tx,
                ];
            }
        });

        return $response;

    }
}
