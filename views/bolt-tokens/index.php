<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Bolt Tokens');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bolt-tokens-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Bolt Tokens'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id_token',
            'id_user',
            'type',
            'status',
            'token_price',
            //'token_ricevuti',
            //'fiat_price',
            //'currency',
            //'item_desc',
            //'item_code',
            //'invoice_timestamp:datetime',
            //'expiration_timestamp:datetime',
            //'rate',
            //'from_address',
            //'to_address',
            //'blocknumber',
            //'txhash',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
