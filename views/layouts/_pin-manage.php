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
