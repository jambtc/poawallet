<!-- MOSTRA FOTOCAMERA PER SCANSIONE QR-CODE -->
<div class="modal fade" id="scrollmodalCamera" tabindex="-1" role="dialog" aria-labelledby="scrollmodalLabelCamera" style="display: none;" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body" id='camera-body'>
				<center>
					<div id="video-content">
					    <video muted playsinline id="qr-video"></video>
						<div id='rounded-box'>&nbsp;</div>
					</div>
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" id='camera-close' data-dismiss="modal">
					<i class="fa fa-reply"></i> <?php echo Yii::t('app','close');?>
				</button>
			</div>
		</div>
	</div>
</div>
