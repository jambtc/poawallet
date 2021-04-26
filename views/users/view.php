<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

\yii\web\YiiAsset::register($this);

// include ('sparkline_js.php');
include ('manage-blockchainscan_js.php');
include ('manage-options_js.php');
include ('manage-pin_js.php');
include ('manage-masterseed_js.php');
include ('manage-push_js.php');


?>
<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','User Profile') ?></h3>
		<!-- <div class="notification">
			<a href="#"><i class="fa fa-plus"></i></a>
		</div> -->
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

	<div class="list-box">
		<div class="list-item">
			<i class="fa fa-link text-primary"></i>
			<em class="seperate"></em>
			<span class="list-item-title"><?php echo Yii::t('app','Scan the blockchain');?>
				<small class="text-muted"></small></span>

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
