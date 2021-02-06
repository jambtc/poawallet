<?php

namespace app\models;

use Yii;
use yii\base\Model;
use Web3\Web3;
use yii\web\Controller;
use yii\validators\Validator;

Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';


// echo "<pre>".print_r(Yii::$classMap,true)."</pre>";
// exit;
//
// use external\webapp;




/**
 * SendTokenForm is the model behind the send token form.
 */
class SendTokenForm extends Model
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
            ['amount', 'number'],
            [['memo'], 'string', 'max' => 500],

            // to is validated by isValidAddress()
            [['to'], 'isValidAddress'],

        ];
    }

    /**
	 * @return array customized attribute labels
	 */
	public function attributeLabels()
	{
		return [
			'from'=>Yii::t('model','from'),
			'to'=>Yii::t('model','to'),
			'amount'=>Yii::t('model','Amount'),
			'memo' => Yii::t('model','Message'),
		];
	}

    /**
	 * @param POST string address the Ethereum Address to be paid
	 */
	public function isValidAddress($attribute, $params)
    {
        $webapp = new \webapp;
		$poaNode = $webapp->getPoaNode();
		if (!$poaNode)
            $this->addError($attribute, 'All Nodes are down...');

		$web3 = new Web3($poaNode);
		$utils = $web3->utils;
		$response = $utils->isAddress($this->to);

        if (!($response))
            $this->addError($attribute, 'Address is incorrect.');

	}
}
