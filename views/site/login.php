<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;


$this->title = 'Login';
?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron pb-5">
        <div class="form-divider"></div>

        <!-- <div class="form-row">
            <div class="btn btn-info circle block  mb-15">

          </div> -->
            <button id='telegram-login-button' type="button" class="button circle block green" data-toggle="modal" data-target="#modal-telegram">
              <i class="fa fa-telegram"></i> Login with TELEGRAM
            </button>
        <!-- </div> -->

        <!-- <div class="form-row">

        </div> -->

        <div class="form-divider"></div>
        <div class="form-label-divider"><span>OR</span></div>
        <div class="form-divider"></div>

        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'layout' => 'horizontal',
            'fieldConfig' => [
        		'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n{error}\n<div class=\"col-lg-8\">{error}</div>",
        		'labelOptions' => ['class' => 'col-lg-1 control-label'],
        	],
        ]); ?>

        <?php $fieldOptions1 = [
            'inputTemplate' => '
                                <div class="form-row-group with-icons">
                                    <div class="form-row no-padding">
                                        <i class="fa fa-envelope"></i>
                                        {input}
                                    </div>
                                </div>',
            'inputOptions' => ['class' => ['widget' => 'form-element']]

        ];
          ?>
          <?php $fieldOptions2 = [
              'inputTemplate' => '
                                  <div class="form-row-group with-icons">
                                      <div class="form-row no-padding">
                                          <i class="fa fa-lock"></i>
                                          {input}
                                      </div>
                                  </div>',
              'inputOptions' => ['class' => ['widget' => 'form-element']]

          ];
            ?>

        <?= $form->field($model, 'username', $fieldOptions1)->textInput(['autofocus' => false, 'autocomplete'=>"off"]) ?>
        <?= $form->field($model, 'password', $fieldOptions2)->passwordInput(['autocomplete'=>"new-password"]) ?>
        <?= $form->field($model, 'oauth_provider')->hiddenInput(['value'=>'mail'])->label(false) ?>

        <div class="txt-left">
            <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
        </div>




        <div class="form-group row">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('app','Login'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                <?= yii\authclient\widgets\AuthChoice::widget([
                     'baseAuthUrl' => ['site/auth'],
                     'popupMode' => false,
                     'options' => [
                         'class' => 'auth-clients-holder'
                     ]
                ]) ?>
            </div>
        </div>
        <div class="form-mini-divider"></div>

        <div class="form-row text-center mt-15 mb-5 text-light">
          <a style="color: #dee2e6;" href="forgot-password.html" data-loader="show">Forgot password?</a>
        </div>


        <div class="form-row txt-center text-light mt-15">
          Don't you have an account yet? <a style="color:#007bff;" href="<?php echo Url::to(['site/register']); ?>" data-loader="show">Sign Up</a>
        </div>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
