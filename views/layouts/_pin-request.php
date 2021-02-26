<!-- RICHIESTA PIN da MAIN Layout-->
<div class="modal fade " id="pinRequestModal" tabindex="-1" role="dialog" aria-labelledby="pinRequestModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="min-width:360px;">
            <div class="modal-header">
                <h5 class="modal-title" id="pinRequestModalLabel"><?php echo Yii::t('app','PIN Request');?></h5>
            </div>
            <div class="modal-body ">
                <center>
                    <input type='hidden' id='pin_password' class='form-control' readonly="readonly"/>
                    <input type='hidden' id='pin_password_confirm' class='form-control' readonly="readonly"/>
                </center>
                <div class="pin-confirm-numpad pin-newframe-numpad"></div>
            </div>
            <div class="modal-footer">
                <div class="form-group">
                    <button type="button" disabled="disabled" class="btn btn-primary disabled " id="pinRequestButton">
                        <i class="fa fa-thumbs-up"></i> <span id="#pinRequestButtonText"><?php echo Yii::t('app','Confirm');?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
