<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;



$this->title = 'Login';
// $this->params['breadcrumbs'][] = $this->title;



// echo "<pre>".print_r($settings,true)."</pre>";
// exit;



?>
<div class="site-login">
  <div class="body-content dash-balance jumbotron pb-5">

    <div class="form-divider"></div>

    <div class="form-row">
      <button id='facebook-login-button' type="button" class="button circle block blue mb-15" data-toggle="modal" data-target="#modal-facebook">
        <i class="fa fa-facebook"></i> Login with FACEBOOK
      </button>

    </div>
    <div class="form-row">
      <button id='google-login-button' type="button" class="button circle block red mb-15" data-toggle="modal" data-target="#modal-google">
        <i class="fa fa-google"></i> Login with GOOGLE
      </button>

    </div>
    <div class="form-row">
      <button id='telegram-login-button' type="button" class="button circle block green" data-toggle="modal" data-target="#modal-telegram">
        <i class="fa fa-telegram"></i> Login with TELEGRAM
      </button>
    </div>

    <div class="form-divider"></div>
    <div class="form-label-divider"><span>OR</span></div>
    <div class="form-divider"></div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-12\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

    <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
    <?= $form->field($model, 'password')->passwordInput() ?>

    <div class="form-group row">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <div class="form-row mt-15 mb-5 text-light">
      <a style="color: #dee2e6;" href="forgot-password.html" data-loader="show">Forgot password?</a>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>
