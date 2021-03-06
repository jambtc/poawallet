<?php
namespace app\daemons;

use Yii;
use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;

use yii\base\Model;
use app\models\BoltWallets;
use app\models\BoltTokens;

use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\Url;

use app\components\WebApp;
use app\components\Settings;
use app\components\Messages;
use Web3\Web3;



class CommandsServer extends WebSocketServer
{
    public $maxBlocksToScan = 17; // 200 IS FOR TESTING AT HOME 1500; //don't touch it (1500)
	public $transactionsFound = [];

	private function setMaxBlocksToScan($maxBlocksToScan){
		$this->maxBlocksToScan = $maxBlocksToScan;
	}
	private function getMaxBlocksToScan(){
		return $this->maxBlocksToScan;
	}
	private function setTransactionsFound($transaction){
        $this->transactionsFound[] = $transaction;
	}
	private function getTransactionsFound(){
        // echo "\r\n Ssto nella fuznione";
        return $this->transactionsFound;
	}


    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_CONNECTED, function(WSClientEvent $e) {
            $e->client->user_id = null;
        });
    }


    /**
    * override method getCommand( ... )
    *
    * For example, we think that all user's message is a command
    */
    protected function getCommand(ConnectionInterface $from, $msg)
    {
        $request = json_decode($msg, true);
        return !empty($request['action']) ? $request['action'] : parent::getCommand($from, $msg);
    }

    public function commandSetUserId(ConnectionInterface $client, $msg)
   {
       $request = json_decode($msg, true);

       if (!empty($request['user_id']) && $user_id = trim($request['user_id'])) {
           $client->user_id = WebApp::decrypt($user_id);
       }

       // ricerca il blocknumber adesso e restituisci il valore
       $result = $this->getBlockNumber($client->user_id);
       $this->log("Subscription with user_id: $client->user_id");

       $client->send(json_encode($result));
   }

   //scrive a video e nel file log le informazioni richieste
   private function log($text){
       // $save = new Save;
       // $save->WriteLog('napay','commands','wt', $text);

       // $filename = Yii::$app->basePath."/logs/blockchain-command.log";
       // $myfile = fopen($filename, "a");
       $time = "\r\n" .date('Y/m/d h:i:s a - ', time());

       echo  $time.$text;
   }

   /**
    * Implement command's method using "command" as prefix for method name
    *
    * method for user's command "ping"
    */
    // recupera il blocknumber attuale della blockchain
     // recupera il blocknumber del wallet utente
     // mostra la differenza dei blocchi
   // function commandGetBlockNumber(ConnectionInterface $client, $msg)
   private function getBlockNumber($user_id)
   {
   		$wallet = BoltWallets::find()
            ->andWhere(['id_user'=>$user_id])
			->one();

   		$return = [
   			 'id'=>time(),
   			 "walletBlocknumber"=>0,
   			 "chainBlocknumber"=>0,
   			 "difference"=>0,
   			 "headerMessage"=>'',
   			 "my_address"=>$wallet->wallet_address,
   			 "relativeTime" =>'',
   			 "success"=>false,
   		];
        $blockLatest = Yii::$app->Erc20->getBlockInfo();

   		//calcolo la differenza tra i blocchi
   		$difference = hexdec($blockLatest->number) - hexdec($wallet->blocknumber);

   		$return = [
   			 'id'=>time(),
   			 "walletBlocknumber"=>$wallet->blocknumber,
   			 "chainBlocknumber"=>$blockLatest->number,
   			 "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
   			 "difference"=> $difference,
   			 "user_address"=>$wallet->wallet_address,
   			 "relativeTime" => Yii::$app->formatter->asDuration($difference),
   			 "success"=>true,
             'message'=>'',
   		];

        return $return;
   	}


    // verifica eventuali transazioni dell'utente a partire dal getBlockNumber
	// dell'utente fino ad arrivare all'ultimo blocco della blockchain
    function commandCheckTransactions(ConnectionInterface $client, $msg)
	{
        // inizializza il transactionsFound
        $this->transactionsFound = [];

        $request = json_decode($msg, true);
		// echo '<pre>'.print_r($request,true).'</pre>';
        $postData = $request['postData'];

        // $filename = Yii::$app->basePath."/web/assets/blockchain-command.log";
		// $myfile = fopen($filename, "a");

        $wallets = BoltWallets::find()
		   ->andWhere(['id_user'=>$client->user_id])
		   ->one();

		$savedBlock = $wallets->blocknumber; //dovrebbe essere già stato salvato in formato hex
		$SEARCH_ADDRESS = strtoupper($postData['search_address']);
        $chainBlock = $postData['chainBlocknumber'];

        // echo '\r\n<pre>il saved blocknumber è'.print_r($savedBlock,true).'</pre>';
        // echo '\r\n<pre>il transactiopn found è'.print_r($this->getTransactionsFound(),true).'</pre>';

        //Carico i parametri della webapp
		$settings = Settings::load();

		// numero massimo di blocchi da scansionare
		// if (isset($settings->maxBlocksToScan))
		// 	$this->setMaxBlocksToScan($settings->maxBlocksToScan);

        $maxBlockToScan = $this->getMaxBlocksToScan();

        // echo '\r\n<pre>il maxBlockToScan è'.print_r($maxBlockToScan,true).'</pre>';

		// Inizio il ciclo sui blocchi
        for ($x=1; $x < $maxBlockToScan;$x++)
		{
            $this->log('Inizio il ciclo: '.$x);

            if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock))
            {
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
			   	// ricerco le informazioni del blocco tramite il suo numero
				$block = Yii::$app->Erc20->getBlockInfo($searchBlock,true);
                // $this->log("Informazioni sul blocco: <pre>".print_r($block,true)."</pre>\n");
                $transactions = $block->transactions;
				// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transactions on block n. $searchBlock: \n".'<pre>'.print_r($transactions,true).'</pre>');
				// $this->log('<pre>'.print_r($transactions,true).'</pre>');
				// exit;

				if (!empty($transactions))
				{
                    $this->log("$x Transaction piena on block n. $searchBlock");

					 // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $transaction)
					{
						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($settings->poa_contractAddress) ){
							$this->log(" : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
						    $this->log(" : è una transazione token...\n");
						    //smart contract
						    $ReceivingType = 'token';
						    // $transactionId = $transaction->hash;
						    // recupero la ricevuta della transazione tramite hash
						    $transactionContract = Yii::$app->Erc20->getReceipt($transaction->hash);

						   if ($transactionContract <> '' && !(empty($transactionContract->logs)))
						   {
							   $this->log(" : è una transazione token non vuota...\n");
							   $receivingAccount = $transactionContract->logs[0]->topics[2];
							   $receivingAccount = str_replace('000000000000000000000000','',$receivingAccount);

							   // verifica se nella transazione ricevi o hai inviato
							   if (strtoupper($receivingAccount) == $SEARCH_ADDRESS || strtoupper($transactionContract->from) == $SEARCH_ADDRESS){
								    $this->log(" : è una transazione token che appartiene all'utente...\n");
								   // $save = new Save;

								   // carica da db tramite hash (che è univoco)
								   $tokens = BoltTokens::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   $this->log(" : Transaction su blockchain esiste, ma non nel db. I\'ll save it in DB....\n");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = Yii::$app->Erc20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $settings->poa_decimals
									   ); // decimali del token
									   $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp in cui è stata minata
									   // la  transazione
									   // NB: il timestamp è quello sul server POA.

									   $blockByHash = Yii::$app->Erc20->getBlockByHash($transaction->blockHash);

									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new BoltTokens;
									   $tokens->id_user = $client->user_id;
							           $tokens->status	= 'complete';
							           $tokens->type	= 'token';
							           $tokens->token_price	= $transactionValue;
							           $tokens->token_ricevuti	= $transactionValue;
							           $tokens->fiat_price		=  abs($rate * $transactionValue);
							           $tokens->currency	= 'EUR';
							           $tokens->item_desc = 'wallet';
							           $tokens->item_code = '0';
							           $tokens->invoice_timestamp = hexdec($blockByHash->timestamp);
							           $tokens->expiration_timestamp = hexdec($blockByHash->timestamp) + 60*15;
							           $tokens->rate = $rate;
							           $tokens->from_address = $transaction->from;
							           $tokens->to_address = $receivingAccount;
							           $tokens->blocknumber = hexdec($transactionContract->blockNumber);
							           $tokens->txhash = $transactionContract->transactionHash;
									   $tokens->memo = '';
									   $tokens->save();

									    $this->log("Saving transaction: <pre>".print_r($tokens->attributes,true)."</pre>\n");
									    $this->log("Transaction errors: <pre>".print_r($tokens->errors,true)."</pre>\n");

									   $dateLN = date("d M `y",$tokens->invoice_timestamp);
							           $timeLN = date("H:i:s",$tokens->invoice_timestamp);




									   $id_user_from = BoltWallets::find()->userIdFromAddress($tokens->from_address);

									   // notifica per chi ha inviato (from_address)
									   $notification = [
										   'type_notification' => 'token',
										   'id_user' => $id_user_from === null ? 1 : $id_user_from,
										   'id_tocheck' => $tokens->id_token,
										   'status' => 'complete',
										   'description' => Yii::t('app','A transaction you sent has been completed.'),
										   // 'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
                                           'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id_token),
										   'timestamp' => time(),
										   'price' => $tokens->token_price,
										   'deleted' => 0,
									   ];

                                       $this->log("quindi salvo il primo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                       $messages= Messages::push($notification);
                                       $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   $id_user_to = BoltWallets::find()->userIdFromAddress($tokens->to_address);

                                       // perchè id_user_to === null  ???
                                       // potrebbe accadere che la trtansazione viene inviata da
                                       // METAMASK o da altra applicazione quindi non trovo
                                       // l'indirizzo di quel particolare user nella tabella
                                       // quindi NON INVIO il messaggio
                                       if ($id_user_to !== null){
                                           $notification['id_user'] = $id_user_to;;
    									   $notification['description'] = Yii::t('app','A transaction you received has been completed.');

                                           $this->log("quindi salvo il secondo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                           $messages= Messages::push($notification);
                                           $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");
                                       } else {
                                           $this->log("quindi NON invio il messaggio al DESTINATARIO, in quanto non trovato in tabella wallet");
                                       }



                                       // imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
                                      $this->setTransactionsFound([
                                          'id_token' => $tokens->id_token,
                                           'pushoptions' => $messages,
                                           'balance' => Yii::$app->Erc20->Balance($postData['search_address']),
                                           'row' => WebApp::showTransactionRow($tokens,$postData['search_address'],true),
                                      ]);


									   // $save->Notification($notification);
									   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Notification saved.");
								   }else{
									    $this->log("Transaction su blockchain trovata ed è anche nel db");
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   $this->log("L'user address è il ricevente (to_address)\n");

										   if ($tokens->token_ricevuti == 0 ){ //&& $tokens->status <> 'complete'){
											    $this->log("ma ha ricevuto 0....\n");

											   $dateLN = date("d M `y",$tokens->invoice_timestamp);
											   $timeLN = date("H:i:s",$tokens->invoice_timestamp);
                                               $tokens->status = 'complete';
                                               $tokens->token_ricevuti = $tokens->token_price;



											   $tokens->update();
											   $this->log("allora ho aggiornato tabella tokens....\n");

                                               //salva la notifica
											   $notification = [
												  'type_notification' => 'token',
												  'id_user' => $client->user_id,
												  'id_tocheck' => $tokens->id_token,
												  'status' => 'complete',
												  'description' => Yii::t('app','You received a new transaction.'),
												  // 'url' => Url::to(["/tokens/view",'id'=>WebApp::encrypt($tokens->id_token)]),
                                                  'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id_token),
												  'timestamp' => time(),
												  'price' => $tokens->token_price,
												  'deleted' => 0,
											  ];

                                              $this->log("quindi salvo messaggio 3\n: <pre>".print_r($notification,true)."</pre>\n");

                                              $messages= Messages::push($notification);
                                              $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

                                              $this->setTransactionsFound([
                                                  'id_token' => $tokens->id_token,
                                                 'pushoptions' => $messages,
                                                 'balance' => Yii::$app->Erc20->Balance($postData['search_address']),
                                                 'row' => WebApp::showTransactionRow($tokens,$postData['search_address'],true),
                                              ]);
                                          }else {
                                              $this->log("la tabella token è già aggiornata e i token sono già stati ricevuti e quindi non faccio nulla: $tokens->token_price\n");
                                          }

									   }else{
										  $this->log("The search address non è dell'utente (to_address) e quindi non faccio nulla....\n");
									   } // notifica al ricevente
								   } // tabella tokens found

							   } // if ricevi o hai inviato
						   }// se transaction contract != ''
					   } //endif 'ethereum or token'
				   } // per ogni transazione trovata
			   } else {
                   $this->log("$x Transaction vuota on block n. $searchBlock");

               }//if not empty transaction

               // echo "\r\n<pre>Fine ricerca transazioni on block n. $searchBlock</pre>";
               //aggiorno il numero dei blocchi sul wallet
               // print_r($searchBlock);
               $wallets->blocknumber = $searchBlock;
               $wallets->update();





            }else{
                // savedBlock +x > chainBlock
                $this->log(">blocchi wallet in pari");
    			break;
            }


       } // ciclo for
       // echo "\r\n<pre>fine ciclo</pre>";
	   // }
	   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Searching for transactions. Latest block #: $searchBlock: \n");

       // echo "\r\n<pre>fine funzione</pre>";
       $difference = hexdec($chainBlock) - hexdec($searchBlock);
       // echo "\r\n<pre>differenza è: $difference</pre>";
       // echo "\r\n<pre>txfound è : ".$this->getTransactionsFound() ."</pre>";
       // echo '\r\n<pre>il transactiopn found finale è'.print_r($this->getTransactionsFound(),true).'</pre>';
       $timeToComplete = time() - hexdec($block->timestamp);

       $txFound = $this->getTransactionsFound();
        // if (!(empty($txFound)))
        //     $this->log("getTransaction: <pre>".print_r($txFound,true)."</pre>\n");


       $return = [
           // 'id'=>time(),
           'success'=>true,
           'message'=>'response from check-transactions',
           // 'difference' => $difference,
           // 'command' => 'getBlockNumber',
           // "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
           "transactions"=>$txFound,
            "walletBlocknumber"=>$searchBlock,
            "chainBlocknumber"=>$chainBlock,
            "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
            "difference"=> $difference,
            "user_address"=>$postData['search_address'],
            "relativeTime" => Yii::$app->formatter->asDuration($timeToComplete),
       ];


       $this->log("<pre>il test found finale è: ".print_r($return,true)."</pre>");




       $client->send(json_encode($return));



    }














}
