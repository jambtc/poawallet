<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Network details');
?>
<div class="h-100 network-details dash-balance">
    <div class="dash-content relative">
		<h3 class="w-text"><?= $this->title ?></h3>
	</div>

    <section class="mt-15 mb-15 container">
    	<div class="coin-box">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-3x fa-network-wired"></i>
                    <div class="ml-10">
                      <h3 class="coin-name"><?= Yii::t('app','Latest block') ?></h3>
                      <small class="network-block-number text-muted"> </small>
                    </div>
                </div>
                <!-- <div>
                    <small class="d-block mb-0 txt-green">
                        <i class="txt-green fa fa-wallet mr-10 mb-5"></i>
                        <span class="network-block-wallet"></span>
                    </small>
                    <small class="text-muted d-block">
                        <i class="fa fa-percentage mr-10 mb-5"></i>
                        <span class="network-block-percentage"></span>
                    </small>
                </div> -->

            </div>
        </div>
        <div class="coin-box mt-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-3x fa-wallet"></i>
                    <div class="ml-10">
                      <h3 class="coin-name"><?= Yii::t('app','Wallet block') ?></h3>
                      <small class="network-block-wallet text-muted"> </small>
                    </div>
                </div>
                <div>
                    <small class="d-block mb-0 txt-green">
                        <i class="txt-green fa fa-percentage mr-10 mb-5"></i>
                        <span class="network-block-percentage"></span>
                    </small>
                    <!-- <small class="text-muted d-block">
                        <i class="fa fa-percentage mr-10 mb-5"></i>
                        <span class="network-block-percentage"></span>
                    </small> -->
                </div>

            </div>
        </div>
        <div class="coin-box mt-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-3x fa-history "></i>
                <div class="ml-10">
                  <h3 class="coin-name"><?= Yii::t('app','Remaining time') ?></h3>
                  <small class="network-block-relativeTime text-muted"> </small>
                </div>
            </div>
        </div>
    </section>


    <div class="form-divider"></div>
    <div class="form-divider"></div>

</div>
