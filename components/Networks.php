<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;

use app\models\StandardBlockchainValues;
use app\models\StandardSmartContractValues;
use app\models\Blockchains;
use app\models\SmartContracts;
use app\models\Nodes;

class Networks extends Component
{
    public static function createDefaultS()
	{
		$default_blockchains = StandardBlockchainValues::find()->all();


		$blockchain = Blockchains::find()->where(['id_user'=>Yii::$app->user->id])->all();
		$smartcontract = SmartContracts::find()->where(['id_user'=>Yii::$app->user->id])->all();
		$nodes = Nodes::find()->where(['id_user'=>Yii::$app->user->id])->one();

		if (empty($blockchain)) {
			foreach ($default_blockchains as $default_blockchain){
				$blockchain = new Blockchains;
				$blockchain->id_user = Yii::$app->user->id;
				$blockchain->denomination = $default_blockchain->denomination;
				$blockchain->chain_id = $default_blockchain->chain_id;
				$blockchain->url = $default_blockchain->url;
				$blockchain->symbol = $default_blockchain->symbol;
				$blockchain->url_block_explorer = $default_blockchain->url_block_explorer;
				$blockchain->zerogas = $default_blockchain->zerogas;
				if (!$blockchain->save()){
					var_dump( $blockchain->getErrors());
					die();
				}

				$default_smartcontract = StandardSmartContractValues::find()->where(['id_blockchain'=>$default_blockchain->id])->one();

				$smartcontract = new SmartContracts;
				$smartcontract->id_user = Yii::$app->user->id;
				$smartcontract->id_blockchain = $blockchain->id;

				$smartcontract->id_contract_type = $default_smartcontract->id_contract_type;
				$smartcontract->denomination = $default_smartcontract->denomination;
				$smartcontract->smart_contract_address = $default_smartcontract->smart_contract_address;
				$smartcontract->decimals = $default_smartcontract->decimals;
				$smartcontract->symbol = $default_smartcontract->symbol;
				if (!$smartcontract->save()){
					var_dump( $smartcontract->getErrors());
					die();
				}
			}
		}
		// inserisco la blockchain INSERITA NEI PARAMS come default
		// Poi l'utente può successivamente cambiarla
		// in tal modo posso utilizzare lo stesso software in più
		// ambiti!
		if (null === $nodes){
			$nodes = new Nodes;
			$nodes->id_user = Yii::$app->user->id;
			$nodes->id_blockchain = Yii::$app->params['default_blockchain'];
			$nodes->id_smart_contract = Yii::$app->params['default_smartcontract'];
			if (!$nodes->save()){
				var_dump( $nodes->getErrors());
				die();
			}
		}
		return $nodes;

	}
}
