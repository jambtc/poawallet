<!-- ABILITA PUSH -->
<div class="modal fade " id="pushEnableModal" tabindex="-1" role="dialog" aria-labelledby="pushEnableModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" role="document">
		<div class="modal-content ">
			<div class="modal-header">
				<h5 class="modal-title" id="pushEnableModalLabel"><?php echo Yii::t('app','Push Notifications');?></h5>
			</div>
			<div class="modal-body ">
                <p class="lead">
                    <h4><?php echo Yii::t('app','Enabling');?>:</h4>
                    <?php echo Yii::t('app','By enabling this setting you will receive <b> push </b> notifications when there are new transactions on your wallet. ');?>
                    <i><?php echo Yii::t('app','Notifications are enabled for each device. To receive notifications on other devices you need to log in from each one, enable it and make sure you are online.');?>
					</i>
                </p>
                <p class="lead text-danger"><?php echo Yii::t('app','Be sure to reply <b>Allow </b>when prompted');?></p>
                <div class="form-divider"></div>
                <p class="lead">
                    <h4><?php echo Yii::t('app','Disabling');?>:</h4>
                    <?php echo Yii::t('app','By disabling <b> push </b> notifications, you will no longer receive messages when there are transactions on your wallet.');?> </b>
					<i> <?php echo Yii::t('app','Disabling push notifications from this device may also eliminate the subscription of any other connected devices.');?> </i>
                </p>
			</div>
			<div class="modal-footer">
                <div class="form-group">
					<button type="button" class="btn btn-secondary js-push-btn-modal-back" data-dismiss="modal" >
						<i class="fa fa-backward"></i> <?php echo Yii::t('app','back');?>
					</button>
				</div>

				<div class="form-group">
					<button type="button" class="js-push-btn btn btn-primary " data-dismiss="modal" >
						<i class="fa fa-thumbs-up"></i> <?php echo Yii::t('app','confirm');?>
					</button>
				</div>
                <div class="form-group">
					<button type="button" class="js-push-btn-remove btn btn-primary " data-dismiss="modal" style="display: none;">
						<i class="fa fa-thumbs-down"></i> <?php echo Yii::t('app','remove');?>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
