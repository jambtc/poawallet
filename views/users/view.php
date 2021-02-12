<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\BoltSocialusers */

//
// var_dump($model);
// exit;

// $this->title = $model->id_social;
// $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bolt Socialusers'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// include ('sparkline_js.php');
include ('pin-manage_js.php');
?>
<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('lang','User Profile') ?></h3>
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
		<!-- <ul class="trader-info-list list-unstyled">
            <li>
				<div class="profile3"><canvas width="59" height="50" style="display: inline-block; width: 59px; height: 50px; vertical-align: top;"></canvas></div>
				<h2><?= $transactions ?></h2>
				<small class="txt-muted"><?= Yii::t('lang','Transactions') ?></small>
			</li>
			<li>
				<div class="profile1"><canvas width="59" height="50" style="display: inline-block; width: 59px; height: 50px; vertical-align: top;"></canvas></div>
				<h2><?= $sent ?></h2>
				<small class="txt-muted"><?= Yii::t('lang','Sent') ?></small>
			</li>
			<li>
				<div class="profile2"><canvas width="59" height="50" style="display: inline-block; width: 59px; height: 50px; vertical-align: top;"></canvas></div>
				<h2><?= $received ?></h2>
				<small class="txt-muted"><?= Yii::t('lang','Received') ?></small>
			</li>

		</ul> -->
	</div>
</section>
<section class="bal-section container mt-30">
	<h4 class="title-main mt-0">
		<ul class="trader-info-list list-unstyled">
		<div class="profile3">
			<canvas width="59" height="50" style="display: inline-block; width: 59px; height: 50px; vertical-align: top;"></canvas>
		</div>
		<h2><?= $transactions ?></h2>
		<small class="txt-muted"><?= Yii::t('lang','Transactions') ?></small>
		</ul>
	</h4>
	<div class="resources-card-wrapper">
		<div class="resources-card mr-10">
            <div class="d-flex flex-column flex-md-row">
              <img src="css/img/content/ex2.png" class="max-w mb-10" alt="">
              <div class="d-flex flex-column ml-md-2">
                <p class="text-muted mb-10 font-weight-medium"><?= Yii::t('lang','Total Income') ?></p>
                <div class="progress">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 57%"></div>
                  </div>
                <h4 class="mt-10 mb-0"><?= $received ?></h4>
              </div>
            </div>
        </div>
        <div class="resources-card ml-10">
            <div class="d-flex flex-column flex-md-row">
              <img src="css/img/content/ex1.png" class="max-w mb-10" alt="">
              <div class="d-flex flex-column ml-md-2">
                <p class="text-muted mb-10 font-weight-medium"><?= Yii::t('lang','Expenses') ?></p>
                <div class="progress">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 67%"></div>
                  </div>
                <h4 class="mt-10 mb-0"><?= $sent ?></h4>
              </div>
            </div>
        </div>
	</div>
</section>

<form id="settingsForm bg-primary">

	<div class="form-divider"></div>
	<div class="form-label-divider"><span><?= Yii::t('lang','Edit settings');?></span></div>
	<div class="form-divider"></div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?= Yii::t('lang','PIN');?><small class="text-muted"></small></span>
			<div class="pincodeslider sweet-check" >
				<input type="checkbox" name="pincodeslider" value="1">
				<div class="outline">
					<span></span>
				</div>
			</div>
			<div class="pincodeslider-remove sweet-check checked" style="display:none;" >
				<input type="checkbox" name="pincodeslider-remove" value="1">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?= Yii::t('lang','Two factors authentication');?> <small class="text-muted"></small></span>

			<div class="sweet-check checked">
				<input type="checkbox" name="2fa" value="1" checked="">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?= Yii::t('lang','Backup Master Seed');?> <small class="text-muted"></small></span>

			<div class="sweet-check checked">
				<input type="checkbox" name="masterseed" value="1" checked="">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('lang','Scan the blockchain');?> <small class="text-muted"></small></span>

			<div class="sweet-check">
				<input type="checkbox" name="documents" value="1">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('lang','Save application on Homepage');?> <small class="text-muted"></small></span>

			<div class="sweet-check">
				<input type="checkbox" name="sourcecode" value="1">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('lang','PUSH notifications');?> <small class="text-muted"></small></span>

			<div class="sweet-check checked">
				<input type="checkbox" name="bookmark" value="1" checked="">
				<div class="outline">
					<span></span>
				</div>
			</div>
		</div>
	</div>

	<div class="list-box">
		<div class="list-item">
			<span class="list-item-title"><?php echo Yii::t('lang','Select language');?>
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
	</div>



	<div class="form-divider"></div>

</form>

<!--  nuovo PIN -->
<div class="modal fade " id="pinNewModal" tabindex="-1" role="dialog" aria-labelledby="pinNewModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content" style="min-width:360px;">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo Yii::t('lang','New PIN');?></h5>
			</div>
			<div class="modal-body ">
				<input type='hidden' id='pin_password' class='form-control' readonly="readonly"/>
        		<div class="pin-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
        		<div class="form-group">
					<button type="button" class="btn btn-secondary text-capitalize" data-dismiss="modal" id="pinNewButtonBack">
						<i class="fa fa-backward"></i> <?php echo Yii::t('lang','back');?>
					</button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled  text-capitalize" id="pinNewButton">
						<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('lang','confirm');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- VERIFICA nuovo PIN -->
<div class="modal fade " id="pinVerifyModal" tabindex="-1" role="dialog" aria-labelledby="pinVerifyModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content" style="min-width:360px;">
			<div class="modal-header">
				<h5 class="modal-title" id="pinVerifyModalLabel"><?php echo Yii::t('lang','PIN Verify');?></h5>
			</div>
			<div class="modal-body ">
				<input type='hidden' id='pin_password_confirm' class='form-control' readonly="readonly"/>
        		<div class="pin-confirm-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary text-capitalize" data-dismiss="modal" id="pinVerifyButtonBack">
						<i class="fa fa-backward"></i> <?php echo Yii::t('lang','back');?>
					</button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary text-capitalize disabled" id="pinVerifyButton">
						<i class="fa fa-thumbs-up"></i> <span id='pinVerifyButtonText'><?php echo Yii::t('lang','confirm');?></span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- RIMUOVI PIN -->
<div class="modal fade " id="pinRemoveModal" tabindex="-1" role="dialog" aria-labelledby="pinRemoveModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
		<div class="modal-content" style="min-width:360px;">
			<div class="modal-header">
				<h5 class="modal-title" id="pinRemoveModalLabel"><?php echo Yii::t('lang','Remove PIN');?></h5>
			</div>
			<div class="modal-body ">
        		<div class="pin-remove-numpad pin-newframe-numpad"></div>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary " data-dismiss="modal" id="pinRemoveButtonBack" >
						<i class="fa fa-backward"></i> <?php echo Yii::t('lang','back');?>
					</button>
				</div>
				<div class="form-group">
					<button type="button" disabled="disabled" class="btn btn-primary disabled" id="pinRemoveButton" >
						<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('lang','confirm');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
