<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('lang','Transaction details') .' - '. $model->id_token;
\yii\web\YiiAsset::register($this);
?>
<main class="margin mt-0">
    <h2><?= Yii::t('lang','Transaction details');?></h2>
    <div class="dash-balance">
        <div class="d-flex align-items-center mt-30">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id_token',
                    // 'id_user',
                    // 'type',
                    // 'status',
                    [
                        'type'=>'raw',
                        'attribute'=>Yii::t('model','status'),
                        'value'=>$model->status,
                        'contentOptions' => ['class' => 'btn btn-secondary']
                    ],
                    'invoice_timestamp:datetime',
                    'token_price',
                    // 'token_ricevuti',
                    // 'fiat_price',
                    // 'currency',
                    'item_desc',
                    'item_code',

                    // 'expiration_timestamp:datetime',
                    // 'rate',
                    'from_address',
                    'to_address',
                    'txhash',
                    'blocknumber',

                ],
            ]) ?>
        </div>

    </div>


</main>
