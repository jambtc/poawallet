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
class WizardWalletForm extends Model
{
    public $seed;
    public $address;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['seed'], 'required'],
            [['seed'], 'string', 'max' => 500],

        ];
    }

    /**
	 * @return array customized attribute labels
	 */
	public function attributeLabels()
	{
		return [
			'seed'=>Yii::t('model','seed'),
		];
	}


}
