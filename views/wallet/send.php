<?php
use yii\helpers\Url;
// use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

use drsdre\wizardwidget;

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

<main class="margin mt-0 ">
    <section class="wallets-list container">
        <div class="wallet-address">
            <div class="d-flex align-items-center mt-30">
              <div class="d-flex flex-grow">
                  <div class="mr-auto">
                      <h1 class="b-val"><i class="fa fa-star"></i> <?= $balance ?> </h1>
                      <p class="g-text mb-0"><?= Yii::t('lang','Total Balance');?></p>
                  </div>
              </div>
            </div>
        </div>
    </section>
    <section class="trans-sec container mb-2 dash-balance">
		<?= \drsdre\wizardwidget\WizardWidget::widget($wizard_config); ?>
	</section>
</main>

<?php ActiveForm::end(); ?>
