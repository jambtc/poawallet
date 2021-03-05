<?php
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

$session = Yii::$app->session;
$string = Yii::$app->security->generateRandomString(32);
$session->set('token-wizard', $string );


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


?>

<div class="wallet-generate">
	<div class="body-content">
		<div class="card bg-primary">
			<div class="card-header">
				<h3 class="text-warning"><?php echo Yii::t('app','Spawn a new seed');?></h3>
			</div>
		  <div class="card-body bg-primary">
		      <div class="form-group">
  				<h3 class="text-light"><?php echo Yii::t('app','This is your new seed.');?></h3>
		      </div>

		      <div class="form-group alert alert-light">
				  <p class="no-copypaste text-lowercase" id='seedText' style="font-weight: bold;  font-size:larger;"></p>
		      </div>
			   <div class="form-group alert alert-warning">
				  <?php
				  echo Yii::t('app',"Write the seed and keep it in a safe place; if you lose it you will not be able to restore your wallet and you will lose all the funds.");
				  ?>
			  </div>
			  <input type='hidden' id='seedInput' />

		  </div>

	    <div class="container">
	      <div class="float-left">
			<a href="<?php echo Url::to(['/wizard/index','token' => $string]) ?>" />
				<button type="button" class="btn btn-secondary btn-md" >
  					<i class="fa fa-backward"></i> <?php echo Yii::t('app','Back');?>
  				</button>
			</a>
	      </div>
		  <div class="float-right">
			<a href="#" class="btn btn-success btn-md" data-popup="seedConfirm">
				<i class="fa fa-forward"></i> <?php echo Yii::t('app','Next');?></a>
	      </div>
	    </div>
			<div class="form-divider"></div>
		</div>

		<!--POPUP HTML CONTENT START -->
    	<div class="popup-overlay" id="seedConfirm"> <!-- if you dont want overlay add class .no-overlay -->
    		<div class="popup-container add">
    			<div class="popup-content">
					<div class="card bg-primary">
						<div class="card-header">
							<h3 class="text-warning"><?php echo Yii::t('app','Verify your seed');?></h3>
						</div>
					  <div class="card-body bg-primary">
						  <div class="form-group">

			  				<p class="text-light"><?php echo Yii::t('app','Please, insert your new seed to verify it is correct.');?></p>
					      </div>

					      <div class="form-group">
					          <?= $form->field($formModel, 'seed')->textarea([
					              'rows' => 6, 'cols' => 50,
												'class' => 'no-copypaste form-control text-lowercase',
												'style'=>"font-weight:bold; font-size:larger;"]) ?>
							  <div class="invalid-feedback alert alert-danger" id="seed-error" ></div>
					      </div>

						  <?= $form->field($formModel, 'address')->hiddenInput()->label(false) ?>

					  </div>

				    <div class="container">
				      <div class="float-left">
						<button type="button" class="btn btn-secondary btn-md btn-back" >
			  				<i class="fa fa-backward"></i> <?php echo Yii::t('app','Back');?>
			  			</button>

				      </div>
					  <div class="float-right">
								<?= Html::Button('<i class="fa fa-thumbs-up"></i> '.Yii::t('app','Confirm'), [
					            'class' => 'btn btn-success btn-md seed-submit disabled',
								'id' => 'seed-submit',
					            'data-method' => 'post',
					            'disabled' => 'disabled'
					            // 'data-confirm' => 'Are you sure?'
					        ]);
					      ?>
				      </div>
				    </div>
						<div class="form-divider"></div>
					</div>
    			</div>
    		</div>
    	</div>
    	<!--POPUP HTML CONTENT END -->
	</div>
</div>



<?php ActiveForm::end(); ?>
