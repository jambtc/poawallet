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
$tokensUrl = Url::to(['transactions/index']);

include ('index_js.php');
?>


<div class="dash-balance">
    <!-- <section class="wallets-list container">
       <div class="wallet-address"> -->
            <div class="d-flex align-items-center mt-10">
              <div class="d-flex flex-grow">
                  <div class="w-100">
                      <div class="ref-card c8">
                          <div class="card-header txt-white">
                              <div class="d-flex justify-content-between">
                                  <div>
                                      <?= Yii::t('app','Balance on: ') ?></br>
                                      <a class="txt-white" href="<?= Url::to(['settings/nodes/update']) ?>">
                                          <?= $node->blockchain->denomination ?>
                                      </a>
                                  </div>
                                  <div>
                                      <a href="<?= $userUrl ?>" class="profile-av p-0">
                                          <img src="<?= $userImage ?>">
                                      </a>
                                  </div>
                              </div>
                          </div>

                          <div class="resources-card-wrapper"
                                style="background-color: rgba(255, 255, 255, 0.2);
                                        border-radius: 10px;
                                ">
                              <div class="resources-card bg-transparent txt-white">
                                  <p class="d-flex">
                                      <p class="d-flex justify-content-start">
                                          Token <small class="ml-1"><i class="fa fa-star star-total-balance fa-sm"></i></small>
                                      </p>
                                      <div>
                                          <span class="h5" id="total-balance">
                                            <?php 
                                            // WebApp::si_formatter($balance) 
                                            echo Yii::$app->formatter->asDecimal(
                                                $balance, 
                                                [
                                                    NumberFormatter::MIN_FRACTION_DIGITS => 0,
                                                    NumberFormatter::MAX_FRACTION_DIGITS => $node->smartContract->decimals,
                                                ]
                                            );
                                            ?>
                                           </span>
                                          
                                          <small><?= $node->smartContract->symbol ?></small>
                                      </div>
                                  </p>
                              </div>
                              <div class="resources-card bg-transparent txt-white">
                                  <p class="d-flex">
                                      <p class="d-flex justify-content-start">
                                          Gas <small class="ml-1"><i class="fab fa-ethereum fa-sm"></i></small>
                                      </p>
                                      <div>
                                          <span class="h5" id="total-balance_gas">
                                            <?php
                                            //WebApp::si_formatter($balance_gas) 
                                            echo Yii::$app->formatter->asDecimal(
                                                $balance_gas,
                                                8
                                                
                                            );
                                             
                                             ?>
                                        </span>

                                          <!-- <span id="total-balance_gas2"><?= $balance_gas ?></span> -->
                                          <small><?= $node->blockchain->symbol ?></small>
                                      </div>
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
                <!-- <div class="serv-item">

                </div> -->
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
        'tableOptions' => ['class' => 'table-98 table table-sm mb-3 mx-1'],
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
