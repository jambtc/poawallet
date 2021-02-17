<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\web\HttpException;
use yii\filters\VerbFilter;

use app\models\BoltWallets;

use Web3\Web3;
use Web3\Contract;
use Web3p\EthereumTx\Transaction;
use Nullix\CryptoJsAes\CryptoJsAes;

Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';
Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

class Erc20Controller extends Controller
{
    public $balance = 0; // token balance
    public $decimals = 0; // decimals into smart contract
    public $noncevalue = 0; // nonce count
    public $blocknumber = 0; // blocknumber
    public $transaction = null;

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
    private function setNonce($noncevalue){
        $this->noncevalue = $noncevalue;
    }
    private function getNonce(){
        return $this->noncevalue;
    }
    private function setBlocknumber($blocknumber){
        $this->blocknumber = $blocknumber;
    }
    private function getBlocknumber(){
        return $this->blocknumber;
    }
    private function setTransaction($transaction){
        $this->transaction = $transaction;
    }
    private function getTransaction(){
        return $this->transaction;
    }
    //recupera lo streaming json dal contenuto txt del body
    private function getJsonBody($response)
    {
        $start = strpos($response,'{',0);
        $substr = substr($response,$start);
        return json_decode($substr, true);
    }


    
    /*
	* This function retrieve the token balance of an address
	*/
	public function Balance($fromAddress)
	{
		$settings = \settings::load();
		$this->setDecimals($settings->poa_decimals);

		// echo '<pre>'.print_r($settings,true).'</pre>';
		// exit;
		$webapp = new \webapp;
		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
			throw new HttpException(404,'All Nodes are down...');

		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$contract = new Contract($web3->provider, $settings->poa_abi);

		$contract->at($settings->poa_contractAddress)->call('balanceOf', $fromAddress, [
			'from' => $fromAddress
		], function ($err, $result) use ($contract, $utils) {
			if ($err !== null) {
				throw new HttpException(404,$err->getMessage());
			}
			// echo '<pre>'.print_r($result,true).'</pre>';
			// exit;
			if (isset($result)) {
				//$balance = (string) $result[0]->value;
				$value = $utils->fromWei($result[0]->value, 'ether');
				$Value0 = (string) $value[0]->value;
				$Value1 = (float) $value[1]->value / pow(10, $this->getDecimals());

				$this->setbalance($Value0 + $Value1);
			}
			// echo '<pre>'.print_r($this->getBalance(),true).'</pre>';
			// exit;
		});

		return $this->getBalance();

	}

}
