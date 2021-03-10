<!-- modal di copia in clipboard -->
<div class="modal fade" id="copyAddressModal" tabindex="-1" role="dialog" aria-labelledby="copyAddressModalLabel" aria-hidden="true" style="display: none;" >
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content bg-info text-light">
			<div class="modal-header">
				<h5 class="modal-title" id="copyAddressModalLabel"><?php echo Yii::t('app','Message');?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<?php echo Yii::t('app','Address copied in clipboard.');?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal" style="min-width:100px; padding:2.5px 10px 2.5px 10px; height:30px;">
					<?php echo Yii::t('app','Close');?>
				</button>
			</div>
		</div>
	</div>
</div>
