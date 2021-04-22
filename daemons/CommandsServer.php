<?php
namespace app\daemons;

use Yii;
use consik\yii2websocket\events\WSClientEvent;
use consik\yii2websocket\WebSocketServer;
use Ratchet\ConnectionInterface;

use yii\base\Model;
use app\models\MPWallets;
use app\models\BoltTokens;
use app\models\NotificationsReaders;
use app\models\Notifications;


use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\Html;


use app\components\WebApp;
use app\components\Settings;
use app\components\Messages;
use Web3\Web3;



class CommandsServer extends WebSocketServer
{
    public $maxBlocksToScan = 17;
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

    // scrive a video
    private function log($text){
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
   		$wallet = MPWallets::find()
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
             'command'=>'check-transactions',
   		];
        $ERC20 = new Yii::$app->Erc20(1);

        $blockLatest = $ERC20->getBlockInfo();

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
             'command'=>'check-transactions',
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

        $wallets = MPWallets::find()
		   ->andWhere(['id_user'=>$client->user_id])
		   ->one();

		$savedBlock = $wallets->blocknumber; //dovrebbe essere già stato salvato in formato hex
		$SEARCH_ADDRESS = strtoupper($postData['search_address']);
        $chainBlock = $postData['chainBlocknumber'];

        // echo '\r\n<pre>il saved blocknumber è'.print_r($savedBlock,true).'</pre>';
        // echo '\r\n<pre>il transactiopn found è'.print_r($this->getTransactionsFound(),true).'</pre>';

        //Carico i parametri della webapp
        $ERC20 = new Yii::$app->Erc20(1);
		$settings = Settings::poa(1);

		// numero massimo di blocchi da scansionare
		// if (isset($settings->maxBlocksToScan))
		// 	$this->setMaxBlocksToScan($settings->maxBlocksToScan);

        $maxBlockToScan = $this->getMaxBlocksToScan();

        // echo '\r\n<pre>il maxBlockToScan è'.print_r($maxBlockToScan,true).'</pre>';

		// Inizio il ciclo sui blocchi
        for ($x=0; $x < $maxBlockToScan;$x++)
		{
            $this->log('Inizio il ciclo: '.$x. ' sul blocco n. 0x'.dechex((hexdec($savedBlock)+$x)));
            $this->log('Il massimo è: 0x'.dechex(hexdec($chainBlock)));

            if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock))
            {
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
			   	// ricerco le informazioni del blocco tramite il suo numero


                $block = Yii::$app->Erc20->getBlockInfo($searchBlock,true);
                // $this->log("Informazioni sul blocco: <pre>".print_r($block,true)."</pre>\n");
                $transactions = $block->transactions;
				// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transactions on block n. $searchBlock: \n".'<pre>'.print_r($transactions,true).'</pre>');
				$this->log('Transazioni è: <pre>'.print_r($transactions,true).'</pre>');
				// exit;

				if (!empty($transactions))
				{
                    $this->log("$x Transaction piena on block n. $searchBlock");

					 // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $transaction)
					{
						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($settings->smart_contract_address) ){
							$this->log(" : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
						    $this->log(" : è una transazione token...\n");
						    //smart contract
						    $ReceivingType = 'token';
						    // $transactionId = $transaction->hash;
						    // recupero la ricevuta della transazione tramite hash
						    $transactionContract = $ERC20->getReceipt($transaction->hash);

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
								   $tokens = Transactions::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   $this->log(" : Transaction su blockchain esiste, ma non nel db. I\'ll save it in DB....\n");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = $ERC20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $settings->decimals
									   ); // decimali del token
									   $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp in cui è stata minata
									   // la  transazione
									   // NB: il timestamp è quello sul server POA.

									   $blockByHash = $ERC20->getBlockByHash($transaction->blockHash);

									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new Transactions;
									   $tokens->id_user = $client->user_id;
							           $tokens->status	= 'complete';
							           $tokens->type	= 'token';
							           $tokens->token_price	= $transactionValue;
							           $tokens->token_received	= $transactionValue;
							           $tokens->invoice_timestamp = hexdec($blockByHash->timestamp);
							           $tokens->expiration_timestamp = hexdec($blockByHash->timestamp) + 60*15;
							           $tokens->from_address = $transaction->from;
							           $tokens->to_address = $receivingAccount;
							           $tokens->blocknumber = $transactionContract->blockNumber;
							           $tokens->txhash = $transactionContract->transactionHash;
									   $tokens->message = '';
									   $tokens->save();

									    $this->log("Saving transaction: <pre>".print_r($tokens->attributes,true)."</pre>\n");
									    $this->log("Transaction errors: <pre>".print_r($tokens->errors,true)."</pre>\n");

									   $dateLN = date("d M `y",$tokens->invoice_timestamp);
							           $timeLN = date("H:i:s",$tokens->invoice_timestamp);

									   $id_user_from = MPWallets::find()->userIdFromAddress($tokens->from_address);

									   // notifica per chi ha inviato (from_address)
									   $notification = [
										   'type' => 'token',
										   'id_user' => $id_user_from === null ? 1 : $id_user_from,
										   'status' => 'complete',
										   'description' => Yii::t('app','A transaction you sent has been completed.'),
                                           'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id),
										   'timestamp' => time(),
										   'price' => $tokens->token_price,
									   ];

                                       $this->log("quindi salvo il primo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                       $messages= Messages::push($notification);
                                       $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   $id_user_to = MPWallets::find()->userIdFromAddress($tokens->to_address);

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
                                          'id_token' => $tokens->id,
                                           'pushoptions' => $messages,
                                           'balance' => Yii::$app->Erc20->Balance($postData['search_address']),
                                           'row' => WebApp::showTransactionRow($tokens,$postData['search_address'],true),
                                      ]);

                                      $this->log("HO salvato il messaggio in memoria");



									   // $save->Notification($notification);
									   // $save->WriteLog('bolt','blockchain','SyncBlockchain',"Notification saved.");
								   }else{
									    $this->log("Transaction su blockchain trovata ed è anche nel db");
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   $this->log("L'user address è il ricevente (to_address)\n");

										   if ($tokens->token_received == 0 ){ //&& $tokens->status <> 'complete'){
											    $this->log("ma ha ricevuto 0....\n");

											   $dateLN = date("d M `y",$tokens->invoice_timestamp);
											   $timeLN = date("H:i:s",$tokens->invoice_timestamp);
                                               $tokens->status = 'complete';
                                               $tokens->token_received = $tokens->token_price;



											   $tokens->update();
											   $this->log("allora ho aggiornato tabella tokens....\n");

                                               //salva la notifica
											   $notification = [
												  'type' => 'token',
												  'id_user' => $client->user_id,
												  'status' => 'complete',
												  'description' => Yii::t('app','You received a new transaction.'),
                                                  'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id),
												  'timestamp' => time(),
												  'price' => $tokens->token_price,
											  ];

                                              $this->log("quindi salvo messaggio 3\n: <pre>".print_r($notification,true)."</pre>\n");

                                              $messages= Messages::push($notification);
                                              $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

                                              $this->setTransactionsFound([
                                                  'id_token' => $tokens->id,
                                                 'pushoptions' => $messages,
                                                 'balance' => $ERC20->Balance($postData['search_address']),
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
               $this->log("Update wallet block number on block n. $searchBlock");


               // echo "\r\n<pre>Fine ricerca transazioni on block n. $searchBlock</pre>";
               //aggiorno il numero dei blocchi sul wallet
               // print_r($searchBlock);
               $wallets->blocknumber = $searchBlock;
               $wallets->update();
               $this->log("ho aggiornato la tabella wallet");

            }else{
                // savedBlock +x > chainBlock
                $this->log("blocchi wallet in pari saved & chainblock ($savedBlock+$x) & $chainBlock");
                $this->log("searchblock $searchBlock");
    			break;
            }


       } // ciclo for
       $this->log(">fine ciclo");
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
            'command'=>'check-transactions',
            "transactions"=>$txFound,
        	'searchFromBlockNumber' => $savedBlock,
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

    /**
	 * Check user notification messages
	 */
    function commandCheckLatest(ConnectionInterface $client, $msg)
    {
        // $this->log("sono in check latest");

        // inizializza il transactionsFound
        $this->latestTransactionsFound = [];

        //carico info del wallet
		$wallets = MPWallets::find()
		   ->andWhere(['id_user'=>$client->user_id])
		   ->one();

        //Carico i parametri della webapp
   		$settings = Settings::poa(1);
        $ERC20 = new Yii::$app->Erc20(1);

		$blockLatest = $ERC20->getBlockInfo();

   		$SEARCH_ADDRESS = strtoupper($wallets->wallet_address);
		$chainBlock = $blockLatest->number;
		$savedBlock = dechex (hexdec($blockLatest->number) -15 );



        // Inizio il ciclo sui blocchi
		for ($x=0; $x <= 15; $x++)
		{
            // $this->log('LATEST Inizio il ciclo: '.$x);
			if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock)){
				//somma del valore del blocco in decimali
				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
			   	// ricerco le informazioni del blocco tramite il suo numero
				$block = $ERC20->getBlockInfo($searchBlock,true);
				$transactions = $block->transactions;
				// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transactions on block n. $searchBlock: \n".'<pre>'.print_r($transactions,true).'</pre>');
				// echo '<pre>'.print_r($transactions,true).'</pre>';
				// exit;

				if (!empty($transactions))
				{
					// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
					foreach ($transactions as $transaction)
					{
						//controlla transazioni ethereum
						if (strtoupper($transaction->to) <> strtoupper($settings->smart_contract_address) ){
							// fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione ether...\n");
							$ReceivingType = 'ether';
					    }else{
						   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token...\n");
						   //smart contract
						   $ReceivingType = 'token';
						   // $transactionId = $transaction->hash;
						   // recupero la ricevuta della transazione tramite hash
						   $transactionContract = $ERC20->getReceipt($transaction->hash);

						   if ($transactionContract <> '' && !(empty($transactionContract->logs)))
						   {
							   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token non vuota...\n");
							   $receivingAccount = $transactionContract->logs[0]->topics[2];
							   $receivingAccount = str_replace('000000000000000000000000','',$receivingAccount);

							   // verifica se nella transazione ricevi o hai inviato
							   if (strtoupper($receivingAccount) == $SEARCH_ADDRESS || strtoupper($transactionContract->from) == $SEARCH_ADDRESS){
								   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : è una transazione token che appartiene all'utente...\n");
								   // $save = new Save;

								   // carica da db tramite hash (che è univoco)
								   $tokens = Transactions::find()->findByHash($transactionContract->transactionHash);

								   // SE da DB è null, è NECESSARIA LA NOTIFICA per chi invia e riceve
								   if (null===$tokens){
									   //$save->WriteLog('bolt','blockchain','SyncBlockchain',"Transaction found but it isn\'t in DB. I\'ll save it in DB.");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found but it isn\'t in DB. I\'ll save it in DB....\n");

									   //salva la transazione
									   $timestamp = 0;
									   $transactionValue = $ERC20->wei2eth(
										   $transactionContract->logs[0]->data,
										   $settings->decimals
									   ); // decimali del token
									   $rate = 1; //eth::getFiatRate('token');

									   // con questa funzione recupero il timestamp in cui è stata minata
									   // la  transazione
									   // NB: il timestamp è quello sul server POA.

									   $blockByHash = $ERC20->getBlockByHash($transaction->blockHash);

									   // salvo la transazione NULL in db. Restituisce object
									   $tokens = new Transactions;
									   $tokens->id_user = $wallets->id_user;
							           $tokens->status	= 'complete';
							           $tokens->type	= 'token';
							           $tokens->token_price	= $transactionValue;
							           $tokens->token_received	= $transactionValue;
							           $tokens->invoice_timestamp = hexdec($blockByHash->timestamp);
							           $tokens->expiration_timestamp = hexdec($blockByHash->timestamp) + 60*15;
							           $tokens->from_address = $transaction->from;
							           $tokens->to_address = $receivingAccount;
							           $tokens->blocknumber = $transactionContract->blockNumber;
							           $tokens->txhash = $transactionContract->transactionHash;
									   $tokens->message = '';
									   $tokens->save();

									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Saving transaction: <pre>".print_r($tokens->attributes,true)."</pre>\n");
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction errors: <pre>".print_r($tokens->errors,true)."</pre>\n");

									   $dateLN = date("d M `y",$tokens->invoice_timestamp);
							           $timeLN = date("H:i:s",$tokens->invoice_timestamp);

									   $id_user_from = MPWallets::find()->userIdFromAddress($tokens->from_address);


									   // notifica per chi ha inviato (from_address)
									   $notification = [
										   'type' => 'token',
										   'id_user' => $id_user_from === null ? 1 : $id_user_from,
										   'status' => 'complete',
										   'description' => Yii::t('app','A transaction you sent has been completed.'),
                                           'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id),
										   'timestamp' => time(),
										   'price' => $tokens->token_price,
									   ];

                                       // $this->log("quindi salvo il primo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                       $messages= Messages::push($notification);
                                       // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

									   // $save->Notification($notification);

									   // notifica per chi riceve (to_address)
									   $id_user_to = MPWallets::find()->userIdFromAddress($tokens->to_address);

                                       // perchè id_user_to === null  ???
                                       // potrebbe accadere che la trtansazione viene inviata da
                                       // METAMASK o da altra applicazione quindi non trovo
                                       // l'indirizzo di quel particolare user nella tabella
                                       // quindi NON INVIO il messaggio
                                       if ($id_user_to !== null){
                                           $notification['id_user'] = $id_user_to;
    									   $notification['description'] = Yii::t('app','A transaction you received has been completed.');

                                            // $this->log("quindi salvo il secondo messaggio\n: <pre>".print_r($notification,true)."</pre>\n");
                                           $messages= Messages::push($notification);
                                            // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");
                                       } else {
                                            // $this->log("quindi NON invio il messaggio al DESTINATARIO, in quanto non trovato in tabella wallet");
                                       }



                                       // imposto l'array contenente le transazioni e che sarà restituito alla funzione chiamante
                                      $this->setLatestTransactionsFound([
                                          'id_token' => $tokens->id,
                                           'pushoptions' => $messages,
										   'balance' => $ERC20->Balance($wallets->wallet_address),
                                           'row' => WebApp::showTransactionRow($tokens,$wallets->wallet_address,true),
                                      ]);

									  // $this->log("l'array settransactionFound è: <pre>".print_r($this->getTransactionsFound(),true)."</pre>\n");




								   }else{
									   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Transaction on blockchain found and it is found also on DB....\n");
									   // se NON è NULL notifico solo il ricevente
									   if (strtoupper($tokens->to_address) == $SEARCH_ADDRESS){
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address is to_address....\n");

										   if ($tokens->token_received == 0 ){ //&& $tokens->status <> 'complete'){
											   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : Ricevuto è 0....\n");

											   $dateLN = date("d M `y",$tokens->invoice_timestamp);
											   $timeLN = date("H:i:s",$tokens->invoice_timestamp);
                                               $tokens->status = 'complete';
                                               $tokens->token_received = $tokens->token_price;

											   $tokens->update();
											    // $this->log("allora ho aggiornato tabella tokens....\n");

                                               //salva la notifica
											   $notification = [
												  'type' => 'token',
												  'id_user' => $client->user_id,
												  'status' => 'complete',
												  'description' => Yii::t('app','You received a new transaction.'),
                                                  'url' => 'index.php?r=tokens/view&id='.WebApp::encrypt($tokens->id),
												  'timestamp' => time(),
												  'price' => $tokens->token_price,
											  ];

                                               // $this->log("quindi salvo messaggio 3\n: <pre>".print_r($notification,true)."</pre>\n");

                                              $messages= Messages::push($notification);
                                               // $this->log("che è: <pre>".print_r($messages,true)."</pre>\n");

                                              $this->setLatestTransactionsFound([
                                                  'id_token' => $tokens->id,
                                                 'pushoptions' => $messages,
												 'balance' => $ERC20->Balance($wallets->wallet_address),
                                                 'row' => WebApp::showTransactionRow($tokens,$wallets->wallet_address,true),
                                              ]);
											  // $this->log("l'array settransactionFound è: <pre>".print_r($this->getTransactionsFound(),true)."</pre>\n");
										   }
									   }else{
										   // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : The search address non è to_address e quindi non faccio nulla....\n");
									   } // notifica al ricevente
								   } // tabella tokens found

							   } // if ricevi o hai inviato
						   }// se transaction contract != ''
					   } //endif 'ethereum or token'
				   } // per ogni transazione trovata
			   }// if not empty transaction

			   //aggiorno il numero dei blocchi sul wallet
			   // print_r($searchBlock);
			   // $wallets->blocknumber = $searchBlock;
			   // $wallets->update();
		   }else{
			   break;
		   }
	   } // ciclo for

       $difference = hexdec($chainBlock) - hexdec($searchBlock);
	   // echo "\r\n<pre>differenza è: $difference</pre>";
	   // echo "\r\n<pre>txfound è : ".$this->getTransactionsFound() ."</pre>";
	   // echo '\r\n<pre>il transactiopn found finale è'.print_r($this->getTransactionsFound(),true).'</pre>';
	   $timeToComplete = time() - hexdec($block->timestamp);

	   $txFound = $this->getLatestTransactionsFound();
	    // if (!(empty($txFound)))
	    //     $this->log("getTransaction: <pre>".print_r($txFound,true)."</pre>\n");


        $return = [
            'success'=>true,
            'command'=>'check-latest',
            "transactions"=>$txFound,
            "walletBlocknumber"=>$searchBlock,
            "chainBlocknumber"=>$chainBlock,
            "headerMessage"=> Yii::t('app', "{n} blocks left.", ['n' => $difference]),
            "difference"=> $difference,
            "user_address"=>$wallets->wallet_address,
            "relativeTime" => Yii::$app->formatter->asDuration($timeToComplete),
        ];

        $this->log("<pre>Latest FINALE è: ".print_r($return,true)."</pre>");
        $client->send(json_encode($return));

    }

    /**
	 * Check user notification messages
	 */
    function commandNotifications(ConnectionInterface $client, $msg)
    {
        $request = json_decode($msg, true);

        $news = NotificationsReaders::find()
 	     		->andWhere(['id_user'=>$client->user_id])
				->latest()
 	    		->all();

        $response['command'] = 'notifications';
        $response['countedRead'] = 0;
        $response['countedUnread'] = 0;
        $response['htmlTitle'] = '';
        $response['htmlContent'] = ''; // ex content
        $response['playSound'] = false;
        $response['playAlarm'] = false;

        foreach ($news as $key => $item) {
		   ($item->alreadyread == 0 ? $response['countedUnread'] ++ : $response['countedRead'] ++);
        }

        $x=1;
        foreach ($news as $key => $item) {
		    if ($x == 1){

			    $response['htmlTitle'] .= '<li>
		 			 <div class="d-flex align-items-center justify-content-between">
		 				 <div class="d-flex align-items-center">
		 				   <div class="coin-name notify-htmlTitle">'
					   . Html::encode(\Yii::t('app',
						   'You have {n,plural,=0{read all messages.} =1{one unread message.} other{# unread messages.}}', ['n' => $response['countedUnread']]
					   ))
					   .'</div>
		 				 </div>
		 				 <div class="notify-readAll">
		 				   <a href="#" onclick="notify.openAllEnvelopes();"><small class="text-muted d-block">'. Yii::t('app','Mark all as read') .'</small></a>
		 				 </div>
		 			   </div>
		 		 </li>';
		    }
		    // Leggo la notifica tramite key
		    $notify = Notifications::findOne($item->id_notification);

		    //$notify = Notifications::model()->findByPk($item->id_notification);
		    $notifi__icon = WebApp::Icon($notify->type);
		    $notifi__color = WebApp::Color($notify->status);

		    // verifico che sia un allarme
		    if ($notify->type == 'alarm' && $item->alreadyread == 0)
			   $response['playAlarm'] = true;


			$parsedurl = parse_url($notify->url);

			$classUnread = '';
			if ($item->alreadyread == 0) {
				$classUnread = 'bg-secondary-light';
			}

			$response['htmlContent'] .= '<li class='.$classUnread.'>
			<a onclick="notify.openEnvelope('.$notify->id.');"
				href="'.htmlentities('index.php?'.$parsedurl['query']).'"
				id="news_'.$notify->id.'">
	   			<div class="d-flex align-items-center justify-content-between">
	                   <div class="d-flex align-items-center">
	                       <div class="notice-icon available" style="min-width:30px;">
	                           <i class="'.$notifi__icon.'"></i>
	                       </div>
	                       <div class="ml-10">
	                         <p class="coin-name">'.Yii::t('app',$notify->description).'</p>

							 <div class="text-right">';
							 // se il tipo notifica è help o contact ovviamente non mostro il prezzo della transazione
							 if ($notify->type <> 'help'
									 && $notify->type <> 'contact'
									 && $notify->type <> 'alarm'
							 ){
								 $response['htmlContent'] .= '<b class="d-block mb-0 float-left txt-dark">'.$notify->price.'</b>';
								 //VERIFICO QUESTE ULTIME 3 TRANSAZIONI PER AGGIORNARE IN REAL-TIME LO STATO (IN CASO CI SI TROVA SULLA PAGINA TRANSACTIONS)
								 // $response['status'][$notify->id_tocheck] = $notify->status;
							 }
							 $response['htmlContent'] .= '
								 <small class="text-muted">'.Yii::$app->formatter->asRelativeTime($notify->timestamp).'</small>
							 </div>


	                       </div>
	                   </div>
	               </div>
			   </a>
	   		</li>';

            $x++;
            if ($x>5)
                break;
        }
        if ($response['countedRead'] == 0 && $response['countedUnread'] == 0){
            $response['htmlContent'] .= '<div class="notifi__title">';
            $response['htmlContent'] .= '<p>' . Yii::t('app','You have no messages to read.') . '</p>';
            $response['htmlContent'] .= '</div>';
        } else {
		    $response['htmlContent'] .= '<li>
   			<div class="d-flex align-items-center justify-content-between">
                   <div class="d-flex align-items-center">
                       <a href="'.htmlentities('index.php?r=messages/index').'" class="text-muted">'.Yii::t('app','Manage notifications').'</a>
                   </div>
               </div>
   		</li>';
	    }

        // $this->log("<pre>notifiche è:: ".print_r($response,true)."</pre>");
        $this->log("<pre>notifiche completato</pre>");
        $client->send(json_encode($response));
	}
}
