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

$sendTokenForm->from = $fromAddress;


?>


<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('lang','Sell') ?></h3>

	</div>
</div>


<section class="bal-section container">
	<div id="content">
	    <div id="content-body">
	    	<div class="content-head">
				<div class="d-flex align-items-center">
				    <div class="d-flex flex-grow">
				        <div class="mr-auto">
				            <p class="mb-0"><?= Yii::t('lang','Total Balance');?></p>
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
                        					<img src="css/img/content/2.png" class="icon" alt="">
                                            {input}
                                        </div>
                        			</div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]

            ];
              ?>
            <!-- DA -->
            <div class="form-group">
              <?= $form->field($sendTokenForm, 'from', $fieldOptions1)->textInput(['readonly'=>true]) ?>
            </div>
    	</div>

    	<div class="form-mini-divider"></div>

    	<div class="txt-left">
            <?php
            $fieldOptions2 = [
                'inputTemplate' => '
                <div class="form-row-group with-icons">
                    <div class="form-row no-padding" >
                        <img src="css/img/content/p3.png" class="icon" alt="" id="activate-camera-btn">
                        {input}
                    </div>
                </div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]
            ];
              ?>
            <div class="form-group">
                <?= $form->field($sendTokenForm, 'to', $fieldOptions2)
                    ->textInput(['autofocus' => true, 'validate' ])
                    ->hint(Yii::t('lang','Insert address or press camera to scan')) ?>
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
                    <a href="#" data-popup="cameraPopup" id="buttonCamera-popup">
                        <img  src="css/img/content/2.png" class="icon" alt="" id="activate-camera-btn">
                    </a>
                    {input}
                    </div>
                </div>',
                'inputOptions' => ['class' => ['widget' => 'form-element']]
            ];
              ?>

            <div class="form-group">
                 <?= $form->field($sendTokenForm, 'amount', $fieldOptions3)->textInput(['type' => 'number']) ?>
                 <?= $form->field($sendTokenForm, 'balance')->hiddenInput(['value' => $balance])->label(false) ?>
            </div>



        </div>
        <div class="form-mini-divider"></div>
        <div class="txt-left">
            <?= $form->errorSummary($sendTokenForm, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
        </div>

    	<div class="form-mini-divider"></div>


    	<div><a href="#" class="button circle block yellow" data-popup="sellOrder" id="getCheckedButton1"><?= Yii::t('lang','Send token') ?></a></div>



        <!--POPUP HTML CONTENT START -->
    	<div class="popup-overlay" id="sellOrder"> <!-- if you dont want overlay add class .no-overlay -->
    		<div class="popup-container add">
    			<div class="popup-content">

    				<img src="css/img/content/crypto-buy.png" class="img-buy" alt="">

    				<h5 class="txt-blue mb-0"><?= Yii::t('lang','Amount that will be sent:') ?></h5>
    				<h3 class="mt-10 mb-10 text-primary"><span class="ml-1 amount-to-send"></span> </h3>

                        <!-- MESSAGGIO -->
                        <?php
                        $fieldOptions4 = [
                            'inputTemplate' => '
                            <label class="col-lg-1 control-label text-left text-dark" for="sendtokenform-memo">'.$sendTokenForm->getAttributeLabel('memo').'</label>
                            <div class="form-row-group with-icons">
                                <div class="form-row no-padding" >
                                    {input}
                                </div>
                            </div>',
                            'inputOptions' => ['class' => ['widget' => 'form-control']]
                        ];
                          ?>
                        <div class="form-group hide-text">
                            <?= $form->field($sendTokenForm, 'memo', $fieldOptions4)->textarea([
                                'rows' => 6, 'cols' => 50])->label(false) ?>
                        </div>

    				<div class="transaction-details list-unstyled " style="display: none;">







    				</div>
    				<div><a href="#" class="more-btn mb-10 pay-submit"><?= Yii::t('lang','Confirm') ?></a></div>
                    <div style="display: none;" class="pay-close float-right"><a  href="<?= Url::to(['/wallet/index'])?> " />
                        <button type="button" class="btn button circle block green"><?= Yii::t('lang','Close') ?></button>
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
        <!--POPUP HTML CONTENT END -->
    </div>






</section>

<?php ActiveForm::end(); ?>
