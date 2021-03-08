<?php

namespace app\models;

use Yii;
use yii\base\Model;
use Web3\Web3;
use yii\web\Controller;
use yii\validators\Validator;

use app\components\WebApp;
use app\components\Settings;


/**
 * SendTokenForm is the model behind the send token form.
 */
class SendForm extends Model
{
    public $from;
	public $to;
	public $amount;
	public $memo;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['from', 'to', 'amount'], 'required'],
            [['amount'], 'number'],
            [['memo'], 'string', 'max' => 500],

            // to is validated by isValidAddress()
            [['to'], 'isValidAddress'],

            // to is validated by validateAmount()
            [['amount'], 'validateAmount'],

        ];
    }

    /**
	 * @return array customized attribute labels
	 */
	public function attributeLabels()
	{
		return [
			'from'=>Yii::t('app','from'),
			'to'=>Yii::t('app','to'),
			'amount'=>Yii::t('app','Amount'),
			'memo' => Yii::t('app','Message'),
		];
	}


    /**
	 * @param POST amount to check decimals in the smart contract
	 */
    public function validateAmount($attribute, $params)
    {
        $settings = Settings::load();
        if ($this->amount == 0)
            $this->addError($attribute, 'There are too decimals.');

        if ((int)$this->amount != $this->amount) {
            if (strlen($this->amount) - strrpos($this->amount, '.') - 1 > $settings->poa_decimals)
                $this->addError($attribute, 'There are too decimals.');
        }
    }

    /**
	 * @param POST string address the Ethereum Address to be paid
	 */
	public function isValidAddress($attribute, $params)
    {
        $WebApp = new WebApp;
		$poaNode = $WebApp->getPoaNode();
		if (!$poaNode)
            $this->addError($attribute, 'All Nodes are down...');

		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$response = $utils->isAddress($this->to);

        if (!($response))
            $this->addError($attribute, 'Address is incorrect.');

	}
}
