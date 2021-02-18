<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

use drsdre\wizardwidget;

$this->title = Yii::$app->id;
?>



<?php



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

$userUrl = Url::to(['users/view','id'=>app\components\WebApp::encrypt(Yii::$app->user->identity->id)]);

include ('send_js.php');



$wizard_config = [
	'id' => 'stepwizard',
	'steps' => [
		1 => [
			'title' => 'Step 1',
			'icon' => 'glyphicon glyphicon-transfer',
			'content' => $this->render('_send-step1',[
							'fromAddress' => $fromAddress,
							'sendTokenForm' => $sendTokenForm,
							'form' => $form,
							'balance' => $balance
						]),
			'buttons' => [
				'save' => [
					'title' => Yii::t('lang','Continue'),
					'options' => [
						'class' => 'btn btn-primary mt-2',
						'style' => 'font-size: initial;'
					],
				 ],

			 ],
			 'skippable' => false,
		],
		// 2 => [
		// 	'title' => 'Step 2',
		// 	'icon' => 'glyphicon glyphicon-cloud-upload',
		// 	'content' => $this->render('_step2',[
		// 					'fromAddress' => $fromAddress,
		// 					'sendTokenForm' => $sendTokenForm,
		// 					'form' => $form,
		// 				]),
		// 	'skippable' => false,
		// ],
		// 3 => [
		// 	'title' => 'Step 3',
		// 	'icon' => 'glyphicon glyphicon-transfer',
		// 	'content' => '<h3>Step 3</h3>This is step 3',
		// ],
	],
	'complete_content' => $this->render('_send-step2',[
					'fromAddress' => $fromAddress,
					'sendTokenForm' => $sendTokenForm,
					'form' => $form,
				]), // Optional final screen
	// 'start_step' => 1, // Optional, start with a specific step
];
?>
<div class="dash-balance ">
    <!-- <section class="wallets-list container">
        <div class="wallet-address"> -->
            <div class="d-flex align-items-center mt-30">
              <div class="d-flex flex-grow">
                  <div class="mr-auto">
					  <h1 class="b-val"><i class="fa fa-star star-total-balance"></i> <span id="total-balance"><?= $balance ?></span> </h1>
 					 <p class="g-text mb-0"><?= Yii::t('lang','Total Balance');?></p>
                  </div>
				  <div class="ml-auto align-self-end">
					  <a href="<?= $userUrl ?>" class="profile-av"><img src="<?= $userImage ?>"></a>
				  </div>
              </div>
            </div>
        <!-- </div>
    </section> -->
	</div>
    <section class="trans-sec container mb-2 dash-balance relative">

		<?= \drsdre\wizardwidget\WizardWidget::widget($wizard_config); ?>

	</section>

<?php ActiveForm::end(); ?>
