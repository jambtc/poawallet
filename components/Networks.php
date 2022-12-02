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
    public static function createDefaults()
	{
		$default_blockchains = StandardBlockchainValues::find()->all();

		foreach ($default_blockchains as $default_blockchain){
			// cerca se già inserita
			$blockchain = Blockchains::find()
			->byUserId(Yii::$app->user->id)
			->byChain($default_blockchain->chain_id)
			->bySymbol($default_blockchain->symbol)
			->one();

			if (null === $blockchain){
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
			}

			$default_smartcontract = StandardSmartContractValues::find()->where(['id_blockchain'=>$default_blockchain->id])->one();

			// cerca se già inserita
			$smartcontract = SmartContracts::find()
			->byUserId(Yii::$app->user->id)
			->byBlockchainId($blockchain->id)
			->one();

			if (null === $smartcontract) {
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

			// cerca se già inserita
			$node = Nodes::find()
			->byUserId(Yii::$app->user->id)
			->one();

			if (null === $node) {
				$node = new Nodes;
				$node->id_user = Yii::$app->user->id;
				$node->id_blockchain = $blockchain->id;
				$node->id_smart_contract = $smartcontract->id;
				if (!$node->save()) {
					var_dump($node->getErrors());
					die();
				}
			}
		}

		// echo '<pre>node in networks' . print_r($node, true);
		// exit;
		
		return $node;

	}
}
