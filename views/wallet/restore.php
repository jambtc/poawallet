<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;


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

include ('js_script.php');


?>

<div class="wallet-generate">
	<div class="body-content">
		<div class="card bg-transparent no-border">
			<div class="card-header">
				<h2>Installazione</h2>
			</div>
		  <div class="card-body bg-primary">
		      <div class="form-group">
				  <h3 class="text-light"><?php echo Yii::t('lang','Restore');?></h3>
  					<p class="text-light"><?php echo Yii::t('lang','Insert your seed to restore the wallet.');?></p>
		      </div>

		      <div class="form-group">
		          <?= $form->field($formModel, 'seed')->textarea([
		              'rows' => 6, 'cols' => 50]) ?>
				  <div class="invalid-feedback alert-danger" id="seed-error" ></div>
		      </div>

			  <?= $form->field($formModel, 'address')->hiddenInput() ?>

		  </div>
		  <div class="card-footer">
  				<button type="button" class="btn btn-secondary btn-lg" >
  					<i class="fa fa-backward"></i> <?php echo Yii::t('lang','back');?>
  				</button>

		      <?= Html::Button('<i class="fa fa-thumbs-up"></i> '.Yii::t('lang','confirm'), [
		            'class' => 'btn btn-primary btn-lg float-right seed-submit',
		            //'data-method' => 'post',
		            // 'data-pjax' => 1
		            // 'data-confirm' => 'Are you sure?'
		        ]);
		      ?>
		  </div>


		</div>
	</div>
</div>



<?php ActiveForm::end(); ?>
