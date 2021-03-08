<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;


$this->title = 'Login';

$options = [
    'resetCookie' => \yii\helpers\Url::to(['/oauthgoogle/reset-cookie']),
];
$this->registerJs(
    "var yiiLoginOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    \yii\web\View::POS_HEAD,
    'yiiLoginOptions'
);

$resetCookie = <<<JS
  $('#google-login-button').on('click', function() {
    $.get(yiiLoginOptions.resetCookie);
  });

JS;

$this->registerJs(
    $resetCookie,
    View::POS_READY, //POS_END
    'resetCookieUrl'
);
?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
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

        <div class="txt-left">
            <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
        </div>

        <div class="form-row text-center mt-15 mb-5 text-light">
          <a style="color: #dee2e6;" href="forgot-password.html" data-loader="show">Forgot password?</a>
        </div>

        <div class="form-mini-divider"></div>


        <div class="form-group row">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton(Yii::t('app','Login with email'), ['class' => 'button circle block orange', 'name' => 'login-button']) ?>
            </div>
        </div>


        <div class="form-row txt-center text-light mt-15">
          Don't you have an account yet? <a style="color:#007bff;" href="<?php echo Url::to(['site/register']); ?>" data-loader="show">Sign Up</a>
        </div>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
