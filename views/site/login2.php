<?php
use yii\helpers\Url;

// require_once Yii::$app->params['libsPath'] . '/Settings.php';
//

$settings = settings::load();
//
// /* @var $this yii\web\View */
//
// // require_once Yii::$app->params['libsPath'] . '/OAuth/oauth-telegram/login.php';
// $checkTelegramAuthorization = Url::to('telegram/CheckAuthorization');
// $bot_username = Settings::load()->telegramBotName;
// $bot_token = Settings::load()->telegramToken;
//
// require_once Yii::$app->params['libsPath'] . '/OAuth/oauth-google/login.php';
// $checkGoogleAuthorization = Yii::app()->createUrl('google/CheckAuthorization');
//
// require_once Yii::$app->params['libsPath'] . '/OAuth/oauth-fb/login.php';
// $facebookAppID = $settings->facebookAppID;
// $facebookAppVersion = $settings->facebookAppVersion;
// $sourceLanguage = explode('_',Yii::app()->sourceLanguage);
// $lingua = $sourceLanguage[0];
// $paese = strtoupper($sourceLanguage[1]);




$this->title = Yii::$app->id;
?>
<div class="site-index">

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

      <div class="form-row-group with-icons">
        <div class="form-row no-padding">
          <i class="fa fa-envelope"></i>
          <input type="email" name="aaa" class="form-element" placeholder="Username or Email">
        </div>
        <div class="form-row no-padding">
          <i class="fa fa-lock"></i>
          <input type="password" name="aaa" class="form-element" placeholder="Password">
        </div>
      </div>

      <div class="form-row txt-center mt-15">
        <a href="forgot-password.html" data-loader="show">Forgot password?</a>
      </div>

      <div class="form-divider"></div>

      <div class="form-row">
        <a href="Javascript:void(0);" class="button circle block orange">Login</a>
      </div>

      <div class="form-row txt-center mt-15">
        Don't you have an account yet? <a href="signup.html" data-loader="show">Sign Up</a>
      </div>

    </div>
</div>

<!-- Google access -->
<div class="modal fade" id="modal-google" tabindex="-1" role="dialog" aria-labelledby="GoogleModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert-primary">
			<div class="modal-header">
				<div class="modal-title text-light" id="GoogleModalLabel"><?php echo Yii::t('app','Sign in with Google'); ?></div>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<center>
						<?php
						// $loginGoogle = new \jambtc\google\Login($checkGoogleAuthorization);
						// echo $loginGoogle->loginButton();
						?>
					</center>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Facebook access -->
<div class="modal fade" id="modal-facebook" tabindex="-1" role="dialog" aria-labelledby="FacebookModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert-primary">
			<div class="modal-header">
				<div class="modal-title text-light" id="FacebookModalLabel"><?php echo Yii::t('app','Sign in with Facebook'); ?></div>
			</div>
			<div class="modal-body">
				<div class="form-group" style="width:100%;">
					<center>
						<?php
						// $loginFB = new \jambtc\facebook\Login($facebookAppID,$facebookAppVersion,$lingua,$paese);
						// echo $loginFB->loginButton();
						?>
					<!-- <fb:login-button
						class="fb-login-button"
						data-size="large"
						data-button-type="login_with"
						data-use-continue-as="true"
  					scope="public_profile,email"
  					onlogin="checkLoginState();">
					</fb:login-button> -->

					<!-- <div class="socialResponseData" class='text-light'></div> -->

				</center>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Telegram access -->
<div class="modal fade" id="modal-telegram" tabindex="-1" role="dialog" aria-labelledby="TelegramModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content alert-primary">
			<div class="modal-header">
				<div class="modal-title text-light" id="TelegramModalLabel"><?php echo Yii::t('app','Sign in with Telegram'); ?></div>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<center>
						<?php
						// $loginTelegram = new \jambtc\telegram\Login($bot_username,$bot_token);
						// echo $loginTelegram->loginButton($checkTelegramAuthorization,'large');
						?>
						<!-- <script async src="https://telegram.org/js/telegram-widget.js?2" data-telegram-login="<?php //echo $bot_username; ?>" data-size="large" data-onauth="onTelegramAuth(user)" data-request-access="write"></script> -->
						<!-- <div class="socialResponseData" class='text-light'></div> -->
					</center>
				</div>
			</div>
		</div>
	</div>
</div>
