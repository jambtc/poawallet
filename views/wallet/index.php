<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

// Yii::$classMap['webapp'] = Yii::getAlias('@packages').'/webapp.php';

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wallet');

$receiveUrl = Url::to(['receive/index']);
$sendUrl = Url::to(['send/index','v'=>time()]);
$userUrl = Url::to(['users/view','id'=>app\components\WebApp::encrypt(Yii::$app->user->identity->id)]);
$tokensUrl = Url::to(['tokens/index']);

include ('index_js.php');

?>


<div class="dash-balance">
    <!-- <section class="wallets-list container">
       <div class="wallet-address"> -->
            <div class="d-flex align-items-center mt-30">
              <div class="d-flex flex-grow">
                  <div class="mr-auto">
                      <h1 class="b-val"><i class="fa fa-star star-total-balance"></i> <span id="total-balance"><?= $balance ?></span> </h1>
                      <p class="g-text mb-0"><?= Yii::t('app','Total Balance');?></p>
                  </div>
                  <div class="ml-auto align-self-end">
                      <a href="<?= $userUrl ?>" class="profile-av"><img src="<?= $userImage ?>"></a>
                  </div>
              </div>
            </div>

           <div class="services-bulk">
             <div class="content-row">
                <div class="serv-item">
                   <a href="<?= $sendUrl ?>" class="serv-icon">
                       <!-- <img src="css/img/content/icon1.png" class="mb-5" alt=""> -->
                       <i class="fa fa-paper-plane fa-lg text-primary"></i>
                   </a>
                   <span><?= Yii::t('app','Send');?> </span>
                </div>
                <div class="serv-item">
                  <a href="<?= $receiveUrl ?>" class="serv-icon">
                      <!-- <img src="css/img/content/icon2.png" class="mb-5" alt=""> -->
                       <i class="fas fa-chevron-circle-down fa-lg text-primary"></i>
                  </a>
                   <span><?= Yii::t('app','Receive');?> </span>
                </div>
                <div class="serv-item">
	                <a href="<?= $tokensUrl ?>" class="serv-icon">
                        <!-- <img src="css/img/content/p2.png" class="mb-5" alt=""> -->
                         <i class="fa fa-list fa-lg text-primary"></i>
                    </a>
	                <span><?= Yii::t('app','Transactions');?></span>
	            </div>
            </div>
           </div>
        <!-- </div>
    </section> -->
</div>

    <section class="trans-sec container mb-2">
      <h4 class="title-main mt-0 text-light"><?= Yii::t('app','Recent Transactions');?></h4>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'showHeader'=> false,
        'tableOptions' => ['class' => 'table-98 table table-sm mb-3 ml-1 mr-1'],
        // 'layout' => "{summary}\n{items}\n{pager}",
        'layout' => "{items}",
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            [
               'attribute'=>'',
               'format' => 'raw',
               'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
               'value' => function ($data) use ($fromAddress) {
                  return app\components\WebApp::showTransactionRow($data,$fromAddress);

               },
            ],

        ],
    ]); ?>

    </section>
</div>
