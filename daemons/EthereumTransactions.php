<?php
namespace app\daemons;

use Yii;
use yii\base\Model;
use app\models\Blockchains;
use app\models\EthtxsStatus;
use app\models\Ethtxs;
use app\models\SmartContracts;

class EthereumTransactions
{
    // scrive a video
    private function log($text){
       $time = "\r\n" .date('Y/m/d h:i:s a - ', time());
       echo  $time.$text;
       // sleep(1);
    }


    public function start() {
        set_time_limit(0); //imposto il time limit unlimited
        $maxBlockToScan = 12; // 1 blocco ogni 2 secondi
        $maxRepeat = 5;

        while (true){
            $distinct = [];
            $blockchains = Blockchains::find()
                //->andWhere(['zerogas'=>1])
                ->orderby(['id'=>SORT_ASC])
                ->all();

            $this->log("");
            $this->log("Inizio il ciclo");

            foreach ($blockchains as $blockchain)
            {
                $distinct[$blockchain->symbol] = $blockchain;
            }

            // $this->log("distinct: <pre>".print_r($distinct,true).'</pre>');
            // die();
            if (empty($distinct)){
                sleep(1);
                continue;
            }

            // distinct così le cerco solo per 1 volta
            foreach ($distinct as $row){

                $this->log("");
                $this->log("");
                $this->log('['.$row->symbol.']'." Blockchain with user_id: $row->id_user");

                //$blockchains[$row->symbol] = $row->url;

                // imposto il componente con il parametro richiesto
                $ERC20 = new Yii::$app->Erc20($row->id_user);

                $a = 1;
                while (true){
                    $blockInfo = $ERC20->getBlockInfo('latest',false,$row->url);
                    // $this->log("blockinfo: <pre>".print_r($blockInfo,true).'</pre>');
                    if (null !== $blockInfo){
                        $chainBlock = $blockInfo->number;
                        break;
                    }
                    $this->log('repeating blockInfo...');
                    $a ++;
                    sleep(1);
                    if ($a > $maxRepeat){
                        break;
                    }
                }

                if ($a > $maxRepeat) {
                    continue;
                }

                $EthTxsStatus = EthtxsStatus::find()->where(['symbol'=>$row->symbol])->one();

                if (null === $EthTxsStatus){
                    $EthTxsStatus = new EthtxsStatus;
                    $EthTxsStatus->symbol = $row->symbol;
                    $EthTxsStatus->blocknumber = '0x0';
                    $EthTxsStatus->save();
                }
                $savedBlock = $EthTxsStatus->blocknumber;
                $this->log("Start block is: ".print_r($savedBlock,true).'');

                // Inizio il ciclo sui blocchi
                for ($x=1; $x < $maxBlockToScan;$x++)
        		{
                    $this->log("");
                    $this->log('['.$row->symbol.']'.' Inizio il ciclo: '.$x. ' sul blocco n. 0x'.dechex((hexdec($savedBlock)+$x)).' ('.(hexdec($savedBlock)+$x).' => '.hexdec($chainBlock).')');
                    if ((hexdec($savedBlock)+$x) <= hexdec($chainBlock))
                    {
                        //$this->log('Il massimo è: 0x'.dechex(hexdec($chainBlock)));
        				//somma del valore del blocco in decimali
        				$searchBlock = '0x'. dechex (hexdec($savedBlock) + $x );
        			   	// ricerco le informazioni del blocco tramite il suo numero

                        // se è una blockchain esterna tipo BNB o MATIC,
                        // esco e non carico le tx. Poi magari più in là...
                        if ($row->zerogas == 0){
                            $EthTxsStatus->blocknumber = $chainBlock;
                            $EthTxsStatus->update();
                            break;
                        }

                        $transactions = [];
                        $a = 1;
                        while (true){
                            $block = $ERC20->getBlockInfo($searchBlock,true,$row->url);

                            // $this->log("blockinfo: <pre>".print_r($blockInfo,true).'</pre>');
                            if (null !== $block){
                                // $this->log("Informazioni sul blocco: <pre>".print_r($block,true)."</pre>\n");
                                $transactions = $block->transactions;
                                break;
                            }
                            $this->log('repeating block...');
                            $a ++;
                            sleep(1);
                            if ($a > $maxRepeat){
                                break;
                            }
                        }
                        if ($a > $maxRepeat) {
                            continue;
                        }

                        if (!empty($transactions))
        				{
                            $this->log("$x Transaction piena on block n. $searchBlock");

        					 // fwrite($myfile, date('Y/m/d h:i:s a', time()) . " : La transazione non è vuota\n");
        					foreach ($transactions as $idx => $trans)
        					{
                                //$this->log("transazione: <pre>".print_r($trans,true)."</pre>\n");
                                $inputinfo = $trans->input;
                                $inputinit = substr($inputinfo,0,10);

                                # Check if transaction is a contract transfer
                                if ($trans->value == '0x0' && $inputinit != '0xa9059cbb') {
                                    continue;
                                }

                                $timestamp = hexdec($block->timestamp);

                                $value = hexdec($trans->value);
                                $value = (string) $value;
                        		$value = $value / (1*pow(10,18)); //1000000000000000000;

                                $txhash = $trans->hash;

                                $fr = $trans->from;
                                $to = $trans->to;
                                $gasprice = $trans->gasPrice;
                                $gas = $trans->gas;
                                $contract_to = '';
                                $contract_value = 0;


                                // echo '<pre>valore timestamp: '.print_r($timestamp,true).'</pre>';
                                // echo '<pre>valore decimale: '.print_r($value,true).'</pre>';
                                // die;

                                # Check if transaction is a contract transfer
                                if ($inputinit == '0xa9059cbb') {
                                    $to = '0x'.substr($inputinfo,34,40);

                                    $contract_to = $trans->to;
                                    $contract_value = hexdec(substr($inputinfo,-64));

                                    // echo '<pre>inizia per 0xa9059cbb'.print_r($trans,true).'</pre>';
                                    // echo '<pre>contract_to: '.print_r($contract_to,true).'</pre>';
                                    // echo '<pre>to: '.print_r($to,true).'</pre>';
                                    // echo '<pre>contract_value: '.print_r($contract_value,true).'</pre>';
                                    // // echo '<pre>token value: '.print_r($tokenValue,true).'</pre>';
                                    // echo '<pre>token value 2: '.print_r(hexdec($contract_value),true).'</pre>';
                                    //
                                    //
                                    // die();
                                    # Correct contract transfer transaction represents '0x' + 4 bytes 'a9059cbb' + 32 bytes (64 chars) for contract address and 32 bytes for its value
                                    //contract_to = inputinfo[10:-64]
                                    //contract_value = inputinfo[74:]
                                }

                                $ethtxs = Ethtxs::find()->where(['txhash'=>$txhash])->one();

                                if (null == $ethtxs){
                                    $ethtxs = new Ethtxs;
                                    $ethtxs->timestamp = $timestamp;
                                    $ethtxs->txfrom = $fr;
                                    $ethtxs->txto = $to;
                                    $ethtxs->gas = $gas;
                                    $ethtxs->gasprice = $gasprice;
                                    $ethtxs->blocknumber = $block->number;
                                    $ethtxs->dec_blocknumber = hexdec($block->number);
                                    $ethtxs->txhash = $txhash;
                                    $ethtxs->value = $value;
                                    $ethtxs->contract_to = $contract_to;
                                    $ethtxs->contract_value = $contract_value;
                                    if (!$ethtxs->save()){
                    					var_dump( $ethtxs->getErrors());
                    					die();
                    				}

                                }


                            }
                        } else {
                            $this->log("$x Transaction vuota on block n. $searchBlock");

                        }//if not empty transaction
                        //$this->log("Update status block number on block n. $searchBlock");

                        // echo "\r\n<pre>Fine ricerca transazioni on block n. $searchBlock</pre>";
                        //aggiorno il numero dei blocchi sul wallet
                        // print_r($searchBlock);
                        $EthTxsStatus->blocknumber = $searchBlock;
                        $EthTxsStatus->update();
                        $this->log("ho aggiornato la tabella status con blocknumber: ".$EthTxsStatus->blocknumber);
                    } else{
                        // savedBlock +x > chainBlock
                        $this->log("blocchi status in pari saved & chainblock ($savedBlock+$x) & $chainBlock");
                        $this->log("searchblock $searchBlock");
            			//break;
                    }
                }
            }

            // if (!(hexdec($searchBlock)) <= hexdec($chainBlock))
            //     sleep($maxBlockToScan/2);
            // echo '<pre>'.print_r($blockchains,true).'</pre>';
            $this->log("finito il ciclo ");
            // sleep(1);

        }
    }


}
