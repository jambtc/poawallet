<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
use app\assets\AccountValueAsset;


// impostazioni variabili
$options = [
    'accountValueArray' => $userAccountValueArray['accountValues'],
];
$this->registerJs(
    "var yiiUserOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    yii\web\View::POS_HEAD,
    'yiiUserOptions'
);

AccountValueAsset::register($this);

include ('manage-blockchainscan_js.php');
include ('manage-options_js.php');
include ('manage-pin_js.php');
include ('manage-masterseed_js.php');
include ('manage-push_js.php');

?>
<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','User Profile') ?></h3>
	</div>
</div>
<section class="container bal-section">
	<div class="form-row txt-center">
		<div class="profile-image">
			<img class="avatar-img" alt="User Avatar" src="<?= $model->picture ?>" width="100" height="100">
			<a href="javascript:void(0);" class="update-btn"><i class="fa fa-camera"></i></a>
		</div>
	</div>

	<div class="trader-info">
		<h3><?= $model->first_name ?> <?= $model->last_name ?></h3>
		<p><?= $model->email ?></p>

	</div>
</section>
<section class="bal-section container mt-30">

	<div class="resources-card-wrapper">
		<div class="resources-card mr-10">
            <div class="d-flex flex-column flex-md-row">
              <img src="css/img/content/ex2.png" class="max-w mb-10" alt="">
              <div class="d-flex flex-column ml-md-2">
                <p class="text-muted mb-10 font-weight-medium"><?= Yii::t('app','Total Income') ?></p>
                <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percent_received ?>%"><?= $received['count'] ?></div>
                  </div>
                <h4 class="mt-10 mb-0"><?= $received['sum'] ?></h4>
              </div>
            </div>
        </div>
        <div class="resources-card ml-10">
            <div class="d-flex flex-column flex-md-row">
              <img src="css/img/content/ex1.png" class="max-w mb-10" alt="">
              <div class="d-flex flex-column ml-md-2">
                <p class="text-muted mb-10 font-weight-medium"><?= Yii::t('app','Expenses') ?></p>
                <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $percent_sent ?>%"><?= $sent['count'] ?></div>
                  </div>
                <h4 class="mt-10 mb-0"><?= $sent['sum'] ?></h4>
              </div>
            </div>
        </div>
	</div>
</section>


<section class="mt-15 mb-15 container">
	<div class="coin-box">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img class="img-xs" src="css/img/content/coin2.png" alt="coin image">
                <div class="ml-10">
                  <h3 class="coin-name"><?= Yii::t('app','Balance') ?></h3>
                  <small class="text-muted"><?= $userAccountValueArray['balance'] ?> </small>
                </div>
            </div>
            <div>
                <small class="d-block mb-0 txt-<?= $userAccountValueArray['color'] ?>">
                    <i class="txt-<?= $userAccountValueArray['color'] ?> fa fa-arrow-<?= $userAccountValueArray['arrow'] ?> mr-10 mb-5"></i>
                    <?= $userAccountValueArray['increase'] ?>%
                </small>
            </div>
        </div>
        <div class="watch-chart mt-15">
            <span class="accountValue"><canvas width="252" height="80" style="display: inline-block; width: 252px; height: 80px; vertical-align: top;"></canvas></span>
        </div>

    </div>
</section>

<form id="settingsForm bg-primary">

	<div class="form-divider"></div>
	<div class="form-label-divider"><span><?= Yii::t('app','Edit settings');?></span></div>
	<div class="form-divider"></div>

	<div class="list-box">
		<div class="list-item">
			<i class="far fa-keyboard text-primary"></i>
			<em class="seperate"></em>
			<span class="list-item-title"><?= Yii::t('app','PIN');?><small class="text-muted"></small></span>
			<div class="pincodeslider sweet-check" >
				<div class="outline">
					<span></span>
				</div>
			</div>
			<div class="pincodeslider-remove sweet-check checked" style="display:none;" >
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box masterseed-box">
		<div class="list-item">
			<i class="fas fa-key text-primary"></i>
			<em class="seperate"></em>
			<span class="list-item-title"><?= Yii::t('app','Show Master Seed');?>
				<small class="text-muted"></small></span>

			<div class="masterseedSlider sweet-check">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?= Yii::t('app','Two factors authentication');?> <small class="text-muted"></small></span>

			<div class="sweet-check checked">
				<input type="checkbox" name="2fa" value="1" checked="">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div> -->





	<!-- <div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('app','Save application on Homepage');?> <small class="text-muted"></small></span>

			<div class="sweet-check">
				<input type="checkbox" name="sourcecode" value="1">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div> -->

	<div class="list-box">
		<div class="list-item">
			<i class="far fa-comment text-primary"></i>
			<em class="seperate"></em>
			<span class="list-item-title"><?php echo Yii::t('app','PUSH notifications');?> <small class="text-muted"></small></span>

			<div class="js-push-btn-modal sweet-check "
					data-toggle="modal"
					data-target="#pushEnableModal">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

    <div class="form-divider"></div>
	<div class="form-label-divider"><span><?= Yii::t('app','Blockchain');?></span></div>
	<div class="form-divider"></div>

    <div class="list-box">
		<div class="list-item">
			<i class="fa fa-network-wired text-primary"></i>
			<em class="seperate"></em>
			<a href="<?= Url::to(['settings/nodes/index'])?>" >
				<span class="list-item-title"><?php echo Yii::t('app','Select nodes');?>
					<small class="text-muted"></small>
				</span>
			</a>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<i class="fa fa-link text-primary"></i>
			<em class="seperate"></em>
			<a href="<?= Url::to(['network/index'])?>" >
				<span class="list-item-title"><?php echo Yii::t('app','Scan network');?>
					<small class="text-muted"></small>
				</span>
			</a>
		</div>
	</div>

    <div class="list-box">
		<div class="list-item">
			<i class="fa fa-link text-info"></i>
			<em class="seperate"></em>
			<a href="<?= Url::to(['/settings/blockchains/index'])?>" >
				<span class="list-item-title"><?php echo Yii::t('app','Manage blockchain');?>
					<small class="text-muted"></small>
				</span>
			</a>
		</div>
	</div>

    <div class="list-box">
		<div class="list-item">
			<i class="fa fa-star text-info"></i>
			<em class="seperate"></em>
			<a href="<?= Url::to(['/settings/tokens/index'])?>" >
				<span class="list-item-title"><?php echo Yii::t('app','Manage tokens');?>
					<small class="text-muted"></small>
				</span>
			</a>
		</div>
	</div>

    <div class="form-divider"></div>
	<div class="form-label-divider"><span><?= Yii::t('app','Experimental');?></span></div>
	<div class="form-divider"></div>

    <div class="list-box">
		<div class="list-item">
			<i class="fa fa-eraser text-danger"></i>
			<em class="seperate"></em>
			<span class="list-item-title text-danger"><?php echo Yii::t('app','Reset blockchain');?>
				<small class="text-muted"></small>
			</span>
			<div class="blockchainRescan sweet-check">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<!-- <div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('app','Select language');?>
	            <small class="text-muted">

	            </small>
	        </span>

	        <div class="form-row-group with-icons">
	                <div class="form-row no-padding">
	                    <i class="fa fa-language"></i>
	                    <select class="form-element">
	                        <option value="" selected="">Select</option>
	                        <option value="1">English</option>
	                        <option value="1">Spanish</option>
	                        <option value="1">Turkish</option>
	                    </select>
	                </div>
	            </div>
		</div>
	</div> -->



	<div class="form-divider"></div>

</form>
