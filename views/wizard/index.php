<?php
use yii\helpers\Url;
// use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

use drsdre\wizardwidget;

$this->title = Yii::$app->id;

$form = ActiveForm::begin([
	'id' => 'wizard-form',
	// 'enableAjaxValidation' => true,
	// 'enableClientValidation' => false,
	'layout' => 'horizontal',
	'fieldConfig' => [
		'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n{error}\n<div class=\"col-lg-8\">{error}</div>",
		'labelOptions' => ['class' => 'col-lg-1 control-label'],
	],

]);

include ('_js.php');

$wizard_config = [
	'id' => 'stepwizard',
	'steps' => [
		1 => [
			'title' => 'Step 1',
			'icon' => 'glyphicon glyphicon-transfer',
			'content' => $this->render('_wizard-step1'),
			'buttons' => [
				'next' => [
					'title' => 'no-title',
					'options' => [
						'class' => 'hidden',
					],
				 ],
			 ],
			 'skippable' => false,
		],
		2 => [
			'title' => 'Step 2',
			'icon' => 'glyphicon glyphicon-cloud-upload',
			'content' => $this->render('_wizard-step2'),
			'skippable' => false,
            'buttons' => [
				'next' => [
					'title' => 'no-title',
					'options' => [
						'class' => 'hidden',
					],
				 ],
                 'prev' => [
 					'title' => 'no-title',
 					'options' => [
 						'class' => 'hidden',
 					],
 				 ],
			 ],
		],
		3 => [
			'title' => 'Step 3',
			'icon' => 'glyphicon glyphicon-cloud-upload',
			'content' => $this->render('_wizard-step3'),
			'skippable' => false,
            'buttons' => [
				'next' => [
					'title' => 'no-title',
					'options' => [
						'class' => 'hidden',
					],
				 ],
                 'prev' => [
 					'title' => 'no-title',
 					'options' => [
 						'class' => 'hidden',
 					],
 				 ],
			 ],
		],
		4 => [
			'title' => 'Step 4',
			'icon' => 'glyphicon glyphicon-transfer',
			'content' => $this->render('_wizard-step4'),
            'skippable' => false,
            'buttons' => [
				'next' => [
					'title' => 'no-title',
					'options' => [
						'class' => 'hidden',
					],
				 ],
                 'prev' => [
 					'title' => 'no-title',
 					'options' => [
 						'class' => 'hidden',
 					],
 				 ],
			 ],
		],


    ],
	// 'complete_content' => $this->render('_generate-step1',[
	// 				// 'fromAddress' => $fromAddress,
	// 				// 'sendTokenForm' => $sendTokenForm,
	// 				'form' => $form,
	// 			]), // Optional final screen
	// 'start_step' => 1, // Optional, start with a specific step
];
?>

<div class="wallet-generate">
	<div class="body-content">
        <?= \drsdre\wizardwidget\WizardWidget::widget($wizard_config); ?>
	</div>
</div>

<?= $form->field($formModel, 'seed')->hiddenInput()->label(false) ?>
<?= $form->field($formModel, 'address')->hiddenInput()->label(false) ?>


<?php ActiveForm::end(); ?>
