<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('app', 'Wallet');
// $this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="bolt-tokens-index">
  <div class="body-content dash-balance jumbotron" style="padding-bottom: 0px;">
    <h2><?= Html::encode($this->title) ?></h2>
    <p>
        <?= Html::a(Yii::t('app', 'Create Bolt Tokens'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->


<main class="margin mt-0">
  <div class="dash-balance">
    <div class="d-flex align-items-center mt-30">
      <div class="d-flex flex-grow">
          <div class="mr-auto">
              <h1 class="b-val"> $2,589.50 </h1>
              <p class="g-text mb-0"><?= Yii::t('lang','Total Balance');?></p>
          </div>
      </div>
   </div>
   <div class="services-bulk">
     <div class="content-row">


	       	<div class="serv-item">

		           <a href="#" class="serv-icon"><img src="css/img/content/icon1.png" class="mb-5" alt=""></a>
		           <span><?= Yii::t('lang','Send');?> </span>
		       </div>
		       <div class="serv-item">

		           <a href="#" class="serv-icon"><img src="css/img/content/icon2.png" class="mb-5" alt=""></a>
		           <span><?= Yii::t('lang','Receive');?> </span>
		       </div>

      </div>
    </div>
</div>



    <section class="trans-sec container">
      <h4 class="title-main mt-0 "><?= Yii::t('lang','Recent Transactions');?></h4>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'transaction-list list-unstyled mb-0'],
        'columns' => [
          // 'value' => ['app\helpers\transactions', 'row'],
            // ['class' => 'yii\grid\SerialColumn'],
            [
               'attribute'=>'Resume',
               'format' => 'raw',
               'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
               'value' => function ($data) use ($fromAddress) {
                  $dateLN = date("d M `y",$data->invoice_timestamp);
                  $timeLN = date("H:i:s",$data->invoice_timestamp);

                  if ($data->from_address == $fromAddress){
                    $price = '- '.$data->token_price;
                    $color = 'red';
                  } else {
                    $price = $data->token_price;
                    $color = 'green';
                  }

                  $row = '<ul class="transaction-list list-unstyled mb-0">
                          <li style="width: 325px;">
							            <div class="d-flex align-items-center justify-content-between">
	                          <div class="d-flex align-items-center" style="float:left;">
	                            <img class="img-xs" style="display:block;" src="css/img/content/coin2.png" alt="coin image">
	                            <div style="margin-left: 50px; margin-top: -36px;">
	                              <h6 class="coin-name">'.substr($data->to_address,0,21).'...</h6>
	                              <small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
	                            </div>
	                          </div>
	                          <div style="float: right;">
                              <small class="d-block mb-0 txt-'.$color.'">'.$price.'</small>
                              </br>
	                            <small class="text-muted d-block">'.$data->status.'</small>
	                          </div>
	                        </div>
                          </li>

					               </ul>';

                   return $row;
               },
            ],
            // array(
            //   'type'=>'raw',
            //   'name'=>'',
            //   'value'=>'WebApp::typeTransaction($data->type)',
            //   'htmlOptions'=>array('style'=>'width:1px;'),
            // ),
            // 'id_token',
            // 'id_user',
            // 'type',
            // 'status',
            // 'token_price',
            //'token_ricevuti',
            //'fiat_price',
            //'currency',
            //'item_desc',
            //'item_code',
            // 'invoice_timestamp:datetime',
            //'expiration_timestamp:datetime',
            //'rate',
            //'from_address',
            // 'to_address',
            //'blocknumber',
            // 'txhash',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

  </section>
</main>
<!--
  </div>
</div> -->
