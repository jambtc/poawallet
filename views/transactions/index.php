<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Transactions');
// $this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="form-divider"></div> -->
<div class="dash-balance ref-card c10">
    <div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','Transaction list');?></h3>
	</div>

    <section class="px-3 mb-5">


        <?php Pjax::begin(); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'showHeader'=> false,
            'tableOptions' => ['class' => 'table-96 table table-sm mb-3 ml-1 mr-1'],
            'columns' => [
                [
                   'attribute'=>'',
                   'format' => 'raw',
                   'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
                   'value' => function ($data) use ($fromAddress) {
                      return app\components\WebApp::showTransactionRow($data,$fromAddress);
                   },
                ],
                // ['class' => 'yii\grid\SerialColumn'],
                //
                // 'id_token',
                // 'id_user',
                // 'type',
                // 'status',
                // 'token_price',
                //
                // ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>

        <?php Pjax::end(); ?>
        <div class="form-divider"></div>
    </section>


</div>
