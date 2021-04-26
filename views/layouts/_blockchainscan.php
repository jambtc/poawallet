<!-- show Blockchain Scan modal dialog-->
<div class="modal fade" id="blockchainScanModal" tabindex="-1" role="dialog" aria-labelledby="blockchainScanModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert alert-danger">
			<div class="modal-header">
				<h5 class="modal-title" id="blockchainScanModalLabel"><?php echo Yii::t('app','Blockchain Reset');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" id="resetSliderAriablockchain">×</span>
				</button>
			</div>
			<div class="modal-body">
				<p><?php echo Yii::t('app','WARNING - This operation tries to restore the transactions history. Scanning may consume a lot of memory and slow down normal phone activity.');?></p>
				<p>
					<?php echo Yii::t('app','Are you sure to continue?');?>
				</p>
			</div>
			<div class="modal-footer">
				<div class="form-group">
					<button type="button" class="blockchainScanSliderBack2 btn btn-secondary" data-dismiss="modal" >
						<i class="fa fa-backward"></i> <?php echo Yii::t('app','Back');?>
					</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal" id="showblockchainScan" data-toggle="modal" data-target="#showblockchainScanModal">
						<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('app','Confirm');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
