<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use app\components\WebApp;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Wallet');

$receiveUrl = Url::to(['receive/index']);
$sendUrl = Url::to(['send/index','v'=>time()]);
$userUrl = Url::to(['users/view','id'=>WebApp::encrypt(Yii::$app->user->identity->id)]);
$tokensUrl = Url::to(['tokens/index']);

include ('index_js.php');
?>


<div class="dash-balance">
    <!-- <section class="wallets-list container">
       <div class="wallet-address"> -->
            <div class="d-flex align-items-center mt-10">
              <div class="d-flex flex-grow">
                  <div class="w-100">
                      <div class="ref-card c1">
                          <div class="card-header txt-white">
                              <?= Yii::t('app','Balance on: ') ?><a class="txt-white" href="<?= Url::to(['settings/nodes/index']) ?>"><?= $node->blockchain->denomination ?></a>
                          </div>
                          <div class="resources-card-wrapper">
                              <div class="resources-card bg-transparent txt-white">
                                  <p class="p-1">
                                      <i class="fa fa-star star-total-balance"></i>
                                      <span class="" id="total-balance"><?= WebApp::number_shorten($balance) ?>&nbsp;<?= $node->smartContract->symbol ?></span>
                                  </p>
                              </div>
                              <div class="resources-card bg-transparent txt-white">
                                  <p class="p-1">
                                      <i class="fab fa-ethereum"></i>
                                      <span class="" id="total-balance_gas"><?= WebApp::number_shorten($balance_gas) ?>&nbsp;<?= $node->blockchain->symbol ?></span>
                                  </p>
                              </div>
                          </div>
                      </div>
                   </div>
              </div>
            </div>

           <div class="services-bulk">
             <div class="content-row">
                <div class="serv-item">
                   <a href="<?= $sendUrl ?>" class="serv-icon btn btn-light p-0">
                       <i class="fa fa-paper-plane fa-lg text-primary"></i>
                   </a>
                   <span><?= Yii::t('app','Send');?> </span>
                </div>
                <div class="serv-item">
                  <a href="<?= $receiveUrl ?>" class="serv-icon btn btn-light p-0">
                       <i class="fas fa-download fa-lg text-primary"></i>
                  </a>
                   <span><?= Yii::t('app','Receive');?> </span>
                </div>
                <div class="serv-item">
	                <a href="<?= $tokensUrl ?>" class="serv-icon btn btn-light p-0">
                         <i class="fa fa-list fa-lg text-primary"></i>
                    </a>
	                <span><?= Yii::t('app','Transactions');?></span>
	            </div>
                <div class="serv-item">
                    <a href="<?= $userUrl ?>" class="serv-icon profile-av p-0">
                        <img src="<?= $userImage ?>">
                    </a>
                    <span><?= Yii::t('app','Profile');?></span>
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
