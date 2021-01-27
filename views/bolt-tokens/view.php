<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = $model->id_token;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bolt Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bolt-tokens-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id_token], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id_token], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_token',
            'id_user',
            'type',
            'status',
            'token_price',
            'token_ricevuti',
            'fiat_price',
            'currency',
            'item_desc',
            'item_code',
            'invoice_timestamp:datetime',
            'expiration_timestamp:datetime',
            'rate',
            'from_address',
            'to_address',
            'blocknumber',
            'txhash',
        ],
    ]) ?>

</div>
