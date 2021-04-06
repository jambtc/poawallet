<?php
use yii\helpers\Url;
// use jambtc\oauthgoogle;
use jambtc\oauthtelegram;
// use jambtc\oauthfacebook;

// use ;

use app\components\Settings;

$checkTelegramAuthorization = Url::to(['oauthtelegram/check-authorization']);
// $checkFacebookAuthorization = Url::to(['oauthfacebook/check-authorization']);
// $checkGoogleAuthorization = Url::to(['oauthgoogle/check-authorization']);

$settings = Settings::load();
?>



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
