<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

use yii\web\Response;
use yii\web\HttpException;
use yii\helpers\Json;

use app\models\MPWallets;

use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Nullix\CryptoJsAes\CryptoJsAes;

use app\components\Settings;
use app\components\WebApp;


class Erc20 extends Component
{
    public $blockchain_id;

    public $balance = 0; // token balance
    public $decimals = 0; // decimals into smart contract
    // public $noncevalue = 0; // nonce count
    public $blocknumber = 0; // blocknumber
    public $transaction = null;


    public function __construct($blockchain_id = 1) {
        $this->blockchain_id = $blockchain_id;
    }

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
    private function setDecimals($decimals){
        $this->decimals = $decimals;
    }
    private function getDecimals(){
        return $this->decimals;
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

    // sign and send a raw token transaction
    public function sendToken($item)
    {
        $tx = (object) $item;

        // echo '<pre>'.print_r($tx,true).'</pre>';
		// exit;
        /**
          * This is fairly straightforward as per the ABI spec
          * First you need the function selector for test(address,uint256) which is the first four bytes of the keccak-256 hash of that string, namely 0xba14d606.
          * Then you need the address as a 32-byte word: 0x000000000000000000000000c5622be5861b7200cbace14e28b98c4ab77bd9b4.
          * Finally you need amount (10000) as a 32-byte word: 0x0000000000000000000000000000000000000000000000000000000000002710
            *	0x03746bfdeacebf4f37e099511c16683df3bac8eb																										 0000000000000000000000000000000000000000000000000000000000000079
        */
        $data_tx = [
            'selector' => '0xa9059cbb', //ERC20	0xa9059cbb function transfer(address,uint256)
            'address' => self::Encode("address", $tx->toAccount), // $receiving_address è l'indirizzo destinatario,
            'amount' => self::Encode("uint", $tx->amount), //$amount l'ammontare della transazione (da moltiplicare per 10^2)
        ];

        $transaction = new Transaction([
            'nonce' => '0x'.dechex($tx->nonce), //è un object BigInteger
            'from' => $tx->from, //indirizzo commerciante
            'to' => $tx->contractAddress, //indirizzo contratto
            'gas' => $tx->gas, // $gas se supera l'importo 0x200b20 va in eerrore gas exceed limit !!!!!!
            'gasPrice' => $tx->gasPrice, // gasPrice giusto?
            'value' => $tx->value,
            'chainId' => $tx->chainId,
            'data' =>  $data_tx['selector'] . $data_tx['address'] . $data_tx['amount'],
        ]);
        $transaction->offsetSet('chainId', $tx->chainId);
        $signed_transaction = $transaction->sign($tx->decryptedSign); // la chiave derivata da json js AES to PHP

        $WebApp = new WebApp;
		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

        $web3 = new Web3($poaNode);

    	$response = null;
        $web3->eth->sendRawTransaction(sprintf('0x%s', $signed_transaction), function ($err, $tx) use (&$response){
            if ($err !== null) {
                $jsonBody = $this->getJsonBody($err->getMessage());

                if ($jsonBody === NULL){
                    throw new HttpException(404,'ERROR: Nonce error count...');
                }else{
                    throw new HttpException(404,$jsonBody['error']['message']);
                }
            }
            $response = $tx;

        });
        return $response;
    }


    // get the transactions number of address
    public function getNonce($address)
    {
        $WebApp = new WebApp;
		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

        $web3 = new Web3($poaNode);

    	$response = null;
		$web3->eth->getTransactionCount($address, function ($err, $nonce) use (&$response) {
			if($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
            $response = gmp_intval($nonce->value);
		});

        return $response;

    }



    /*
	* This function retrieve the token balance of an address
	*/
	public function Balance($fromAddress)
	{
		$settings = Settings::poa($this->blockchain_id);
		$this->setDecimals($settings->decimals);

		// echo '<pre>'.print_r($settings,true).'</pre>';
		// exit;
		$WebApp = new WebApp;
		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$contract = new Contract($web3->provider, $settings->smart_contract_abi);

		$contract->at($settings->smart_contract_address)->call('balanceOf', $fromAddress, [
			'from' => $fromAddress
		], function ($err, $result) use ($contract, $utils) {
			if ($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
			// echo '<pre>'.print_r($result,true).'</pre>';
			// exit;
			if (isset($result)) {
				//$balance = (string) $result[0]->value;
				$value = $utils->fromWei((string)$result[0]->value, 'ether');
				$Value0 = (string) $value[0]->value;
				$Value1 = (float) $value[1]->value / pow(10, $this->getDecimals());

				$this->setBalance($Value0 + $Value1);
			}
			// echo '<pre>'.print_r($this->getBalance(),true).'</pre>';
			// exit;
		});

		return $this->getBalance();

	}



    public function getBlockInfo($blocknumber = 'latest', $search = false)
    {
		$WebApp = new WebApp;
		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
            return $this->json(['error'=>'All Nodes are down...']);
			// throw new HttpException(404,'All Nodes are down...');

		$web3 = new Web3($poaNode);

		$response = null;
		$web3->eth->getBlockByNumber($blocknumber,$search, function ($err, $block) use (&$response){
			if ($err !== null) {
				return $this->json(['error'=>$err]);
			}
			$response = $block;
		});
        return $response;
    }

    public function getReceipt($hash)
    {
        $settings = Settings::poa($this->blockchain_id);
		$WebApp = new WebApp;

		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
			return $this->json($return);

		$web3 = new Web3($poaNode);
		$contract = new Contract($web3->provider, $settings->smart_contract_abi);

		$response = null;
        $contract->eth->getTransactionReceipt($hash, function ($err, $receipt) use (&$response){
            if ($err !== null) {
          		return $this->json($err);
            }
            $response = $receipt;
        });
        return $response;
    }

    public function getBlockByHash($hash)
    {
        $settings = Settings::poa($this->blockchain_id);
		$WebApp = new WebApp;

		$poaNode = $WebApp->getPoaNode($this->blockchain_id);
		if (!$poaNode)
			return $this->json($return);

		$web3 = new Web3($poaNode);

		$response = null;
        $web3->eth->getBlockByHash($hash, true, function ($err, $block) use (&$response){
            if ($err !== null) {
          		return $this->json($err);
            }
            $response = $block;
        });
        return $response;
    }


    /*
	 * funzione che riceve il valore della transazione nello smart contract
	 * in formato: hex (0x000000000000000000000000000000000000000000000000000000000000012c)
	 * e la trasforma in un numero intero, più i decimali del token
	 */
	public function wei2eth($wei,$decimals)
	{
		$value = substr_replace('0x','0',$wei);
		$array = str_split($wei);
		$number = '';
		$flag = false;
		foreach($array as $digit){
			if ($digit != '0' && $flag == false){
				$number .= $digit;
				$flag = true;
			}
			if ($flag == true)
				$number .= $digit;

		}
		return hexdec($number) / pow(10, $decimals);
	}

    /* funzione per codificare il valore $value del tipo $type in hex */
	private function Encode(string $type, $value): string {
		 $len = preg_replace('/[^0-9]/', '', $type);

		 if (!$len) {
			 $len = null;
		 }

		 $type = preg_replace('/[^a-z]/', '', $type);
		 switch ($type) {
			 case "hash":
			 case "address":
				 if (substr($value, 0, 2) === "0x") {
					 $value = substr($value, 2);
				 }
				 break;
			 case "uint":
			 case "int":
				 //$value = BcMath::DecHex($value);
				 $value = dechex($value);
				 break;
			 case "bool":
				 $value = $value === true ? 1 : 0;
				 break;
			 case "string":
				 $value = self::Str2Hex($value);
				 break;
			 default:
				 echo 'Cannot encode value of type '. $type;
				 break;
		 }
		 return substr(str_pad(strval($value), 64, "0", STR_PAD_LEFT), 0, 64);
	}

    public function loadGas($address)
    {
        $settings = Settings::poa($this->blockchain_id);
        $WebApp = new WebApp;

        $poaNode = $WebApp->getPoaNode($this->blockchain_id);
        if (!$poaNode)
            return $this->json($return);

        $web3 = new Web3($poaNode);

        //recupero il balance
        $balance = 0;
        $web3->eth->getBalance($address, function ($err, $response) use (&$balance){
            $jsonBody = $this->getJsonBody($err);

            if ($jsonBody !== NULL){
                throw new HttpException(404,$jsonBody['error']['message']);
            }
            $balance = $response->toString() ;
        });
        $value = (string) $balance * 1;
		$balance = round ($value / (1*pow(10,18)), 4); //1000000000000000000;

        $response = null;
        if ($balance <= 0) {
            // preparing items
            $fromAccount = $settings->sealer_address; //'0x654b98728213cf1e20e90b1942fdc5597984eb70'; // node1 fujitsu gabcoin
            $amount = 1;
            $toAccount = $address;
            $hex = dechex(21004);
            $gas = '0x'.$hex;

            $prv_key = $WebApp::decrypt($settings->sealer_private_key); //'8303D3CA466B73ED5A65DCAB439947793426172C7777F29A8DB68586D4A079D6'; // chiave privata gabcoin Node1 fujitsu server

            // recupero la nonce per l'account
            $nonce = $this->getNonce($fromAccount);
            $transaction = new Transaction([
                'nonce' => '0x'.dechex($nonce), //è un object BigInteger
                'from' => $fromAccount, //indirizzo sealer Blockchain
                'to' => $toAccount, //indirizzo commerciante
                'gas' => '0x200b20', // gas necessario per la transazione
                'gasPrice' => '1000', // gasPrice giusto?
                'value' => 1 * pow(10, 18),
                'chainId' => $settings->chain_id,
                'data' =>  '0x0', // non ci sono dati per contratto
            ]);
            $transaction->offsetSet('chainId', $settings->chain_id);
            $signed_transaction = $transaction->sign($prv_key); //

            $web3->eth->sendRawTransaction(sprintf('0x%s', $signed_transaction), function ($err, $tx) use (&$response){
                if ($err !== null) {
                    $jsonBody = $this->getJsonBody($err->getMessage());

                    if ($jsonBody === NULL){
                        throw new HttpException(404,'ERROR: Nonce error count...');
                    }else{
                        throw new HttpException(404,$jsonBody['error']['message']);
                    }
                }
                $response = $tx;
            });
        }

        return ['balance' => $balance, 'tx' => $response];

    }
}
