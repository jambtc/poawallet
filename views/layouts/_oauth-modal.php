<?php
use yii\helpers\Url;
use jambtc\oauthgoogle;
use jambtc\oauthtelegram;
use jambtc\oauthfacebook;

use app\components\Settings;

$checkTelegramAuthorization = Url::to(['oauthtelegram/check-authorization']);
$checkFacebookAuthorization = Url::to(['oauthfacebook/check-authorization']);
$checkGoogleAuthorization = Url::to(['oauthgoogle/check-authorization']);

$settings = Settings::load();
?>
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
            $loginTelegram = new jambtc\oauthtelegram\telegram($settings->MegapayTelegramBotName,$settings->MegapayTelegramToken);
            echo $loginTelegram->loginButton($checkTelegramAuthorization,'large');
            ?>
          </center>
        </div>
      </div>
    </div>
  </div>
</div>
