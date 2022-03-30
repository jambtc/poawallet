<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app', 'Network info');
?>
<div class="h-100 network-details ref-card c7">
    <div class="mt-5 ref-card c10">
		<h3 class="w-text"><?= $this->title ?></h3>
	</div>

    <section class="mt-15 mb-15 container">
    	<div class="coin-box">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-2x fa-network-wired"></i>
                    <div class="ml-10">
                      <h3 class="coin-name"><?= Yii::t('app','Node info') ?></h3>
                      <div class="d-flex flex-column">
                          <div class="p-0"><?= Yii::t('app','Url: ') ?><small class="text-muted"><?= $node->blockchain->url ?? null ?></small></div>
                          <div class="p-0"><?= Yii::t('app','Network: ') ?><small class="text-muted"><?= $node->blockchain->denomination ?? null?></small></div>
                          <div class="p-0"><?= Yii::t('app','Token: ') ?><small class="text-muted"><?= $node->smartContract->denomination ?? null?></small></div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="coin-box mt-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-2x fa-project-diagram"></i>
                    <div class="ml-10">
                      <h3 class="coin-name"><?= Yii::t('app','Latest block') ?></h3>
                      <small class="d-block mt-1 mr-3 text-break network-block-hash">
                          &nbsp;
                      </small>
                    </div>
                </div>
                <div>
                    <small class="d-block mb-0 p-1 shadow">
                        <span class="network-block-number text-muted">&nbsp;</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="coin-box mt-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="fas fa-2x fa-wallet"></i>
                    <div class="ml-10">
                      <h3 class="coin-name"><?= Yii::t('app','Wallet block') ?></h3>
                      <small class="d-block mt-1 mr-3 text-break network-block-wallet-hash">
                          &nbsp;
                      </small>
                    </div>
                </div>
                <div>
                    <small class="d-block mb-0 p-1 shadow">
                        <span class="network-block-percentage txt-green">&nbsp;</span>
                        <span class="network-block-wallet text-muted">&nbsp;</span>
                    </small>
                </div>
            </div>

        </div>
        <div class="coin-box mt-3">
            <div class="d-flex align-items-center">
                <i class="fas fa-2x fa-history "></i>
                <div class="ml-10">
                  <h3 class="coin-name"><?= Yii::t('app','Remaining time') ?></h3>
                  <small class="network-block-relativeTime text-muted">&nbsp;</small>
                </div>
            </div>
        </div>
    </section>


    <div class="form-divider"></div>

</div>
