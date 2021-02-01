<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

use jambtc\oauthtelegram;
use jambtc\oauthgoogle;
use jambtc\oauthfacebook;

$this->title = 'Login';
// $this->params['breadcrumbs'][] = $this->title;

$settings = settings::load();

// echo "<pre>".print_r($settings,true)."</pre>";
// exit;

$checkTelegramAuthorization = Url::to(['oauthtelegram/check-authorization']);
$checkGoogleAuthorization = Url::to(['oauthgoogle/check-authorization']);
$checkFacebookAuthorization = Url::to(['oauthfacebook/check-authorization']);

?>
<div class="site-login">
  <div class="body-content">

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
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
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

    <div class="form-row txt-center mt-15">
      <a href="forgot-password.html" data-loader="show">Forgot password?</a>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>

<!-- Facebook access -->
<div class="modal fade" id="modal-facebook" tabindex="-1" role="dialog" aria-labelledby="FacebookModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content alert-primary txt-extra-bold text-primary">
			<div class="modal-header">
				<div class="modal-title" id="FacebookModalLabel"><?php echo Yii::t('app','Sign in with Facebook'); ?></div>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<center>
						<?php
            $sourceLanguage = explode('-',\Yii::$app->language);
            $language = $sourceLanguage[0];
            $country = strtoupper($sourceLanguage[1]);

            $loginFB = new jambtc\oauthfacebook\facebook(
                $settings->facebookAppID,
                $settings->facebookAppVersion,
                $language,
                $country,
                $checkFacebookAuthorization);
						echo $loginFB->loginButton();
						?>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Google access -->
<div class="modal fade" id="modal-google" tabindex="-1" role="dialog" aria-labelledby="GoogleModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content alert-primary txt-extra-bold text-primary">
			<div class="modal-header">
				<div class="modal-title" id="GoogleModalLabel"><?php echo Yii::t('app','Sign in with Google'); ?></div>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<center>
						<?php
					  $loginGoogle = new jambtc\oauthgoogle\google($checkGoogleAuthorization);
						echo $loginGoogle->loginButton();
						?>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Telegram access -->
<div class="modal fade" id="modal-telegram" tabindex="-1" role="dialog" aria-labelledby="TelegramModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content alert-primary txt-extra-bold text-primary">
      <div class="modal-header">
        <div class="modal-title" id="TelegramModalLabel"><?php echo Yii::t('app','Sign in with Telegram'); ?></div>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <center>
            <?php
            $loginTelegram = new jambtc\oauthtelegram\telegram($settings->telegramBotName,$settings->telegramToken);
            echo $loginTelegram->loginButton($checkTelegramAuthorization,'large');
            ?>
          </center>
        </div>
      </div>
    </div>
  </div>
</div>
