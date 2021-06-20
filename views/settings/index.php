<?php

use yii\helpers\Html;
use yii\helpers\Url;


include ('manage-blockchainscan_js.php');

?>
<div class="h-100 ref-card c5">
	<div class="mt-5 ref-card c5">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Settings') ?></h3>
		</div>
	</div>


	<form id="settingsForm bg-primary" >

		<div class="list-box">
			<div class="list-item">
				<i class="fa fa-link text-primary"></i>
				<em class="seperate"></em>
				<a href="<?= Url::to(['network/index'])?>" >
					<span class="list-item-title"><?php echo Yii::t('app','Network info');?>
						<small class="text-muted"></small>
					</span>
				</a>
			</div>
		</div>

		<div class="list-box">
			<div class="list-item">
				<i class="fa fa-network-wired text-primary"></i>
				<em class="seperate"></em>
				<a href="<?= Url::to(['settings/nodes/update'])?>" >
					<span class="list-item-title"><?php echo Yii::t('app','Select nodes');?>
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

		<div class="list-box">
			<div class="list-item">
				<i class="fa fa-link text-info"></i>
				<em class="seperate"></em>
				<a href="<?= Url::to(['/settings/blockchains/index'])?>" >
					<span class="list-item-title"><?php echo Yii::t('app','Manage networks');?>
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

			<div class="form-divider"></div>

	</form>
</div>
