<!-- show Master Seed -->
<div class="modal fade" id="showMasterSeedModal" tabindex="-1" role="dialog" aria-labelledby="showMasterSeedModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert alert-danger">
			<div class="modal-header">
				<h5 class="modal-title" id="showMasterSeedModalLabel"><?php echo Yii::t('app','Master Seed');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" id="resetSliderAriaMaster1">×</span>

				</button>
			</div>
			<div class="modal-body">
				<p id='masterSeed' class="alert alert-info shadow txt-bold no-copypaste"></p>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<button type="button" class="masterseedSliderBack3 btn btn-secondary" data-dismiss="modal" >
						<?php echo Yii::t('app','Close');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- show Master Seed modal dialog-->
<div class="modal fade" id="masterSeedModal" tabindex="-1" role="dialog" aria-labelledby="masterSeedModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert alert-danger">
			<div class="modal-header">
				<h5 class="modal-title" id="masterSeedModalLabel"><?php echo Yii::t('app','Backup Master Seed');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" id="resetSliderAriaMaster2">×</span>

				</button>
			</div>
			<div class="modal-body">
				<div class="masterSeedMessagePinEnabled" style="display:none;">
	                <p><?php echo Yii::t('app','WARNING - It is dangerous to show your Master Seed. Continue only if it is necessary because you have lost your previous backup.');?></p>
					<p>
						<?php echo Yii::t('app','Are you sure to continue?');?>
					</p>
				</div>
				<div class="masterSeedMessagePinDisabled" style="display:none;">
	                <p><?php echo Yii::t('app','WARNING - You can backup your seed only if secure PIN is enabled.');?></p>
				</div>
			</div>
			<div class="modal-footer">
				<div class="masterSeedMessagePinEnabled" style="display:none;">
					<div class="form-group">
						<button type="button" class="masterseedSliderBack2 btn btn-secondary" data-dismiss="modal" >
							<i class="fa fa-backward"></i> <?php echo Yii::t('app','back');?>
						</button>
						<button type="button" class="btn btn-primary" data-dismiss="modal" id="showMasterSeed" data-toggle="modal" data-target="#showMasterSeedModal" style="min-width: 100px; padding:2.5px 10px 2.5px 10px; height:30px;">
							<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('app','confirm');?>
						</button>
					</div>
				</div>
				<div class="masterSeedMessagePinDisabled" style="display:none;">
					<div class="form-group">
						<button  type="button" class="masterseedSliderBack1 btn btn-secondary" data-dismiss="modal">
							<?php echo Yii::t('app','Close');?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
