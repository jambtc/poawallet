<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wallet');
?>


<main class="margin mt-0">
    <section class="wallets-list container">
    <!-- <div class="dash-balance container"> -->
        <div class="wallet-address">
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
    </section>

    <section class="trans-sec container mb-2">
      <h4 class="title-main mt-0 "><?= Yii::t('lang','Recent Transactions');?></h4>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'showHeader'=> false,
        'tableOptions' => ['class' => 'table table-sm mb-3 ml-1 mr-1'],
        // 'layout' => "{summary}\n{items}\n{pager}",
        'layout' => "{items}",
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
               'attribute'=>'',
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
                  ($data->type == 'token') ? $coinImg = 'coin5' : 'coin2';

                  $line = '
                  <a href="'.Url::to(['bolt-tokens/view', 'id' => $data->id_token]).'" />
                  <div class="container-fluid m-0 p-0">
                        <div class="row">
                            <div class="col-12 m0 p-0">
                                <div class="card shadow">
                                    <div class="transaction-card-horizontal">
                                        <div class="img-square-wrapper">
                                            <img class="img-xxs pl-1 pt-2" src="css/img/content/'.$coinImg.'.png" alt="coin image">
                                        </div>
                                        <div class="transaction-card-body ml-1">
                                            <h6 class="card-title pt-2">'.substr($data->to_address,0,21).'...</h6>
                                            <p class="card-text">
                                            <small class="text-muted">'.$dateLN.' <span class="ml-10">'.$timeLN.'</span></small>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <b class="d-block mb-0 text-center txt-'.$color.'">'.$price.'</b>
                                            <small class="text-capitalize text-muted text-center">'.$data->status.'</small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
                    ';

                   return $line;
               },
            ],

        ],
    ]); ?>

    </section>
</main>
