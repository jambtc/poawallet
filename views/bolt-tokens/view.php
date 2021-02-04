<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('lang','Transaction details') .' - '. $model->id_token;
\yii\web\YiiAsset::register($this);
?>
<main class="margin mt-0">
    <section class="wallets-list container">
		<div class="wallet-address">
        	<h3 class="w-text mb-30 mt-0"><?= Yii::t('lang','Transaction details');?></h3>
        	<div class="txt-left">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table g-text'],
                    'attributes' => [
                        'id_token',
                        // 'id_user',
                        // 'type',
                        // 'status',
                        [
                            'type'=>'raw',
                            'attribute'=>Yii::t('model','status'),
                            'value'=>$model->status,
                            'contentOptions' => ['class' => ($model->status == 'complete') ? 'btn btn-success text-capitalize' : 'btn btn-secondary text-capitalize']
                        ],
                        'invoice_timestamp:datetime',
                        'token_price',
                        // 'token_ricevuti',
                        // 'fiat_price',
                        // 'currency',
                        // 'item_desc',
                        // 'item_code',

                        // 'expiration_timestamp:datetime',
                        // 'rate',
                        // 'from_address',
                        [
                            'type'=>'raw',
                            'attribute'=>Yii::t('model','from_address'),
                            'value'=>$model->from_address,
                            'contentOptions' => ['class' => 'text-break']
                        ],
                        // 'to_address',
                        [
                            'type'=>'raw',
                            'attribute'=>Yii::t('model','to_address'),
                            'value'=>$model->to_address,
                            'contentOptions' => ['class' => 'text-break']
                        ],
                        // 'txhash',
                        [
                            'type'=>'raw',
                            'attribute'=>Yii::t('model','txhash'),
                            'value'=>$model->txhash,
                            'contentOptions' => ['class' => 'text-break']
                        ],
                        'blocknumber',

                    ],
                ]) ?>
        	    <!-- <label class="g-text">Select Cryptocurrency</label>
		        <div class="form-row-group with-icons">
					<div class="form-row no-padding">
						<img src="img/content/1.png" class="icon" alt="">

						<select class="form-element">
							<option value="" selected="">Bitcoin</option>
							<option value="1">Ethereum</option>
							<option value="1">Dashcoin</option>
							<option value="1">Ripple</option>
						</select>
					</div>
				</div> -->
        	</div>

            <div class="form-mini-divider"></div>
        </div>
    </section>


</main>
