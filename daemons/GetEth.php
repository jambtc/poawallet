<?php
namespace app\daemons;

use Yii;
use yii\base\Model;
use yii\db\Query;



class GetEth
{
    // scrive a video
    private function log($text,$writelog = false){
       $time = "\r\n" .date('Y/m/d h:i:s a - ', time());
       echo  $time.$text;

       if ($writelog){
           $logFileName = Yii::$app->basePath."/logs/geteth.log";
           $handlefile = fopen($logFileName, "a");
           fwrite($handlefile, $time.$text);
       }
       // sleep(1);
    }


    public function start() {
        set_time_limit(0); //imposto il time limit unlimited

        while (true){


            $this->log("");
            $this->log("Inizio il ciclo");

            $url = 'https://mainnet.infura.io/v3/'.Yii::$app->params['infura_client'];


            // imposto il componente con il parametro richiesto
            $eth = new Yii::$app->Ethereum($url);


            $my_address = Yii::$app->params['my_address'];

            $address[1] = Yii::$app->params['sealers'][1]['address'];
            $address[2] = Yii::$app->params['sealers'][2]['address'];
            $address[3] = Yii::$app->params['sealers'][3]['address'];

            $stepFee = 0.000001;
            $maxFee = 0.0004;
            $startFee = 0.00009;

            // Inizio il ciclo
            while (true)
        	{
                foreach ($address as $id => $addr){
                    $balance = $eth->gasBalance($addr);
                    $this->log("Balance of ".$addr ." is: $balance");
                    if ($balance > 0){
                        $fee = $startFee;
                        while (true){
                            $balance -= $fee;
                            $this->log("Balance of ".$addr ." minus fee (".$fee.") is: $balance", true);
                            if ($balance > 0){
                                $response = $eth->loadGas($id,$my_address,$balance);
                                if ($response['success'] == true){
                                    $this->log("Amount of ".$balance." sent succesfully. Transaction hash is: ". $response['tx'], true);
                                    break;
                                } else {
                                    $this->log("Error: ". $response['message'], true);
                                }
                                $fee += $stepFee;
                                if ($fee > $maxFee){
                                    $this->log("Fee is over max fee. Exit from the loop!", true);
                                    $this->log("Cannot send balance of ".$balance, true);
                                    break;
                                }
                            } else {
                                $this->log("Balance of ".$addr ." is minor or equal to 0: $balance", true);
                                break;
                            }
                        }
                    }
                }
                $maxsleep = 60;
                while ($maxsleep > 0){
                    $this->log("... $maxsleep");
                    sleep(1);
                    $maxsleep --;
                }
            }
        }
    }
}
