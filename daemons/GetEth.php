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

            // Inizio il ciclo
            while (true)
        	{
                foreach ($address as $id => $addr){
                    $balance = $eth->gasBalance($addr);
                    $this->log("Balance of ".$addr ." is: $balance");
                    if ($balance > 0){
                        $this->log("Balance of ".$addr ." is: $balance", true);
                        $response = $eth->loadGas($id,$my_address,$balance);
                        if ($response['success'] == true){
                            $this->log("Transaction hash is: ". $response['tx'], true);
                        } else {
                            $this->log("Error: ". $response['message'], true);
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
