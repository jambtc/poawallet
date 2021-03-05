<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('app','Transaction details') .' - '. $model->id_token;
\yii\web\YiiAsset::register($this);

include ('view_js.php');
?>
<div class="dash-balance">
    <div class="ref-card c2 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Transaction details');?></h3>
			<!-- <div class="notification">
				<a href="#"><i class="fa fa-plus"></i></a>
			</div> -->
		</div>
	</div>
    <section class="trans-sec mt-0 purp" style="padding:15px 0px 0px 0px !important;">

		<div class="ref-card ">
			<div class="d-flex align-items-center">
                <div class="d-flex flex-grow">
                  <div class="mr-auto">
                    <!-- <h1 class="b-val"> $1,249.03 </h1>
                    <p class="g-text mb-10">Ready to Payout</p>
                    <div class="badge badge-pill"> This month 07.44% <i class="txt-green fa fa-arrow-up ml-10"></i></div> -->
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table txt-left d-flex flex-grow '],
                        'attributes' => [
                            // 'id_token',
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','id_token'),
                                'value'=>app\components\WebApp::encrypt($model->id_token),
                                'contentOptions' => ['class' => 'text-break']
                            ],
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','status'),
                                'value'=>$model->status,
                                'contentOptions' => [
                                    'class' => ($model->status == 'complete') ?
                                        'trans-get button circle block green text-capitalize'
                                        : 'trans-get button circle block gray text-capitalize',
                                    'id' => 'trans-get-'.$model->id_token
                                ]
                            ],
                            'invoice_timestamp:datetime',
                            'token_price',
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','from_address'),
                                'value'=>$model->from_address,
                                'contentOptions' => ['class' => 'text-break']
                            ],
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','to_address'),
                                'value'=>$model->to_address,
                                'contentOptions' => ['class' => 'text-break']
                            ],
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','txhash'),
                                'value'=>$model->txhash,
                                'contentOptions' => ['class' => 'text-break']
                            ],
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','blocknumber'),
                                'value'=>'0x'.dechex($model->blocknumber),
                                'contentOptions' => ['class' => 'text-break']
                            ],
                            // '',

                        ],
                    ]) ?>
                  </div>
                </div>
             </div>
		</div>

	</section>





</div>
