<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\WebApp;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('app','Transaction details') .' - '. $model->id;
\yii\web\YiiAsset::register($this);

include ('view_js.php');
?>
<div class="dash-balance ref-card c10">
    <div class="ref-card c2 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Transaction details');?></h3>
			<!-- <div class="notification">
				<a href="#"><i class="fa fa-plus"></i></a>
			</div> -->
		</div>
	</div>
    <section class="bg-transparent trans-sec mt-0" style="padding:15px 0px 0px 0px !important;">

		<div class=" ">
			<div class="d-flex align-items-center">
                <div class="d-flex flex-grow">
                  <div class="mr-auto">
                    <!-- <h1 class="b-val"> $1,249.03 </h1>
                    <p class="g-text mb-10">Ready to Payout</p>
                    <div class="badge badge-pill"> This month 07.44% <i class="txt-green fa fa-arrow-up ml-10"></i></div> -->
                    <?= DetailView::widget([
                        'model' => $model,
                        'options' => ['class' => 'table txt-white txt-left d-flex flex-grow '],
                        'attributes' => [
                            // 'id_token',
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','Transaction ID'),
                                'value'=>WebApp::encrypt($model->id),
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
                                    'id' => 'trans-get-'.$model->id
                                ]
                            ],
                            'invoice_timestamp:datetime',
                            // 'token_price',
                            [
                                'format' => 'raw',
                                'attribute' => 'token_price',
                                'value' => function ($data) {
                                    return WebApp::number_shorten($data->token_price).'<span class="mr-2">'.$data->smartContract->symbol.'</span>';
                                },
                            ],
                            [
                                'format' => 'raw',
                                'attribute' => 'from_address',
                                'value' => function ($data) {
                                    return Html::a($data->from_address, $data->smartContract->blockchain->url_block_explorer.'/account/'.$data->from_address,
                                        [
                                            'class' => 'btn btn-primary btn-sm center-block text-break ',
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                            ],
                            // [
                            //     'type'=>'raw',
                            //     'attribute'=>Yii::t('app','from_address'),
                            //     'value'=>$model->from_address,
                            //     'contentOptions' => ['class' => 'text-break']
                            // ],
                            // [
                            //     'type'=>'raw',
                            //     'attribute'=>Yii::t('app','to_address'),
                            //     'value'=>$model->to_address,
                            //     'contentOptions' => ['class' => 'text-break']
                            // ],
                            [
                                'format' => 'raw',
                                'attribute' => 'to_address',
                                'value' => function ($data) {
                                    return Html::a($data->to_address, $data->smartContract->blockchain->url_block_explorer.'/account/'.$data->to_address,
                                        [
                                            'class' => 'btn btn-primary btn-sm center-block text-break ',
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                            ],

                            [
                                'format' => 'raw',
                                'attribute' => 'txhash',
                                'value' => function ($data) {
                                    return Html::a($data->txhash, $data->smartContract->blockchain->url_block_explorer.'/tx/'.$data->txhash,
                                        [
                                            'class' => 'btn btn-primary btn-sm center-block text-break ',
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                            ],
                            // [
                            //     'type'=>'raw',
                            //     'attribute'=>Yii::t('app','blocknumber'),
                            //     'value'=>$model->blocknumber,
                            //     'contentOptions' => ['class' => 'text-break']
                            // ],
                            [
                                'format' => 'raw',
                                'attribute' => 'blocknumber',
                                'value' => function ($data) {
                                    return Html::a($data->blocknumber, $data->smartContract->blockchain->url_block_explorer.'/block/'.hexdec($data->blocknumber),
                                        [
                                            'class' => 'btn btn-primary btn-sm center-block text-break ',
                                            'target' => '_blank'
                                        ]
                                    );
                                },
                            ],
                            [
                                'type'=>'raw',
                                'attribute'=>Yii::t('app','memo'),
                                'value'=>$model->message,
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
