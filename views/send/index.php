<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

use drsdre\wizardwidget;
use app\components\WebApp;

$this->title = Yii::$app->id;



$form = ActiveForm::begin([
	'id' => 'send-form',
	'enableAjaxValidation' => true,
	'enableClientValidation' => false,
	'layout' => 'horizontal',
	'fieldConfig' => [
		'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n{error}\n<div class=\"col-lg-8\">{error}</div>",
		'labelOptions' => ['class' => 'col-lg-1 control-label'],
	],

]);

include ('send_js.php');
include ('qrcodescanner_js.php');
// include ('nfc-reader_js.php');

$sendForm->from = $fromAddress;
?>


<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','Send token') ?></h3>

	</div>
</div>


<section class="bal-section container">
	<div id="content">
	    <div id="content-body">
	    	<div class="content-head">
				<div class="d-flex align-items-center">
				    <div class="d-flex flex-grow">
				        <div class="mr-auto">
				            <p class="mb-0"><?= Yii::t('app','Total Balance');?></p>
				        </div>
				        <div class="ml-auto align-self-end">
				            <h3 class="text-muted mt-0 mb-0"><i class="fa fa-star star-total-balance"></i> <span id="total-balance"><?= $balance ?></span> </h3>
				        </div>
				    </div>
				</div>
			</div>
	    </div>
	</div>
</section>

<section class="wallets-list container">
	<div class="wallet-address">


    	<div class="txt-left">
            <?php
            $fieldOptions1 = [
                'inputTemplate' => '
                    <div class="form-row-group with-icons">
        				<div class="form-row no-padding">
        					<i class="fas fa-wallet text-primary"></i>
                            {input}
                        </div>
        			</div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]

            ];
              ?>
            <!-- DA -->
            <div class="form-group">
              <?= $form->field($sendForm, 'from', $fieldOptions1)->textInput(['readonly'=>true]) ?>
            </div>
    	</div>

    	<div class="form-mini-divider"></div>

    	<div class="txt-left">
            <?php
            $fieldOptions2 = [
                'inputTemplate' => '
                <div class="form-row-group with-icons">
                    <div class="form-row no-padding" >
						<i id="activate-camera-btn" class="fa fa-camera text-primary"></i>
                        {input}
                    </div>
                </div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]
            ];
			// $fieldOptions2 = [
            //     'inputTemplate' => '
            //     <div class="form-row-group with-icons">
            //         <div class="form-row no-padding" >
			// 			<i id="activate-camera-btn" class="fa fa-camera text-primary"></i>
			// 			// <span id="activate-nfc-reader" class="ml-5">
			// 			// <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
		    //             //     <path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H4V4h16v16zM18 6h-5c-1.1 0-2 .9-2 2v2.28c-.6.35-1 .98-1 1.72 0 1.1.9 2 2 2s2-.9 2-2c0-.74-.4-1.38-1-1.72V8h3v8H8V8h2V6H6v12h12V6z"/>
		    //             // </svg>
			// 			// </span>
            //             {input}
            //         </div>
            //     </div>',
            //     'inputOptions' => ['class' => ['widget' => 'form-element']]
            // ];
              ?>
            <div class="form-group">
                <?= $form->field($sendForm, 'to', $fieldOptions2)
                    ->textInput(['autofocus' => true, 'validate' ])
                    ->hint(Yii::t('app','Insert address or press camera to scan')) ?>
            </div>
    	</div>

    	<div class="form-mini-divider"></div>

    	<div class="txt-left calc-crr">
            <!-- IMPORTO -->
            <?php
            $fieldOptions3 = [
                'inputTemplate' => '
                <div class="form-row-group with-icons">
                    <div class="form-row no-padding" >
						<i class="fas fa-star text-primary"></i>

                    {input}
                    </div>
                </div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]
            ];
              ?>

            <div class="form-group">
                 <?= $form->field($sendForm, 'amount', $fieldOptions3)->textInput(['type' => 'number']) ?>
                 <?= $form->field($sendForm, 'balance')->hiddenInput(['value' => $balance])->label(false) ?>
            </div>



        </div>
        <div class="form-mini-divider"></div>
        <div class="txt-left">
            <?= $form->errorSummary($sendForm, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
        </div>

    	<div class="form-mini-divider"></div>


    	<div><a href="#" class="button circle block yellow" data-popup="sellOrder" id="getCheckedButton1"><?= Yii::t('app','Send token') ?></a></div>



        <!--POPUP HTML CONTENT START -->
    	<div class="popup-overlay" id="sellOrder"> <!-- if you dont want overlay add class .no-overlay -->
    		<div class="popup-container add">
    			<div class="popup-content">

    				<img src="css/img/content/crypto-buy.png" class="img-buy" alt="">

    				<h5 class="txt-blue mb-0"><?= Yii::t('app','Amount that will be sent:') ?></h5>
    				<h3 class="mt-10 mb-10 text-primary"><span class="ml-1 amount-to-send"></span> </h3>

                        <!-- MESSAGGIO -->
                        <?php
                        $fieldOptions4 = [
                            'inputTemplate' => '
                            <label class="col-lg-1 control-label text-left text-dark" for="sendform-memo">'.$sendForm->getAttributeLabel('memo').'</label>
                            <div class="form-row-group with-icons">
                                <div class="form-row no-padding" >
                                    {input}
                                </div>
                            </div>',
                            'inputOptions' => ['class' => ['widget' => 'form-control']]
                        ];
                          ?>
                        <div class="form-group hide-text">
                            <?= $form->field($sendForm, 'memo', $fieldOptions4)->textarea([
                                'rows' => 6, 'cols' => 50])->label(false) ?>
                        </div>

    				<div class="transaction-details list-unstyled " style="display: none;">
    				</div>
    				<div><a href="#" class="more-btn mb-10 pay-submit"><?= Yii::t('app','Confirm') ?></a></div>
                    <div style="display: none;" class="mt-3 pay-close float-right"><a  href="<?= Url::to(['/wallet/index'])?> " />
                        <button type="button" class="btn button circle block green"><?= Yii::t('app','Close') ?></button>
                        </a>
                    </div>
    			</div>
    		</div>
    	</div>
    	<!--POPUP HTML CONTENT END -->
        <!-- POPUP HTML CAMERA QRCODE READER -->
        <div class="popup-overlay" id="cameraPopup">
            <div class="popup-container add">
                <div class="popup-content txt-center">
                    <div class="modal-header">
						<h3 class="text-secondary"><?php echo Yii::t('app','Camera scan'); ?></h3>
                        <button id='camera-close' type="button" class="close float-right" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div  class="modal-body" id='camera-body'>
                        <center>
                            <div id="video-content" class="embed-responsive embed-responsive-4by3 text-center">
                                <video muted playsinline id="qr-video"></video>
                                <div id='rounded-box'>&nbsp;</div>
                            </div>
                        </center>
                    </div>

                </div>
            </div>
        </div>
        <!--POPUP CAMERA QRCODE END -->
		<!-- POPUP NFC READER -->
        <div class="popup-overlay" id="nfcReaderPopup">
            <div class="popup-container add">
                <div class="popup-content txt-center">
                    <div class="modal-header">
						<h3 class="text-secondary"><?php echo Yii::t('app','NFC scan'); ?></h3>
                        <button id='nfc-close' type="button" class="close float-right" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div  class="modal-body" id='nfc-body'>
                        <center>
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48">
			                    <path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H4V4h16v16zM18 6h-5c-1.1 0-2 .9-2 2v2.28c-.6.35-1 .98-1 1.72 0 1.1.9 2 2 2s2-.9 2-2c0-.74-.4-1.38-1-1.72V8h3v8H8V8h2V6H6v12h12V6z"/>
			                </svg>
                        </center>
                    </div>

                </div>
            </div>
        </div>
        <!--POPUP NFC CONTENT END -->
    </div>






</section>

<?php ActiveForm::end(); ?>
