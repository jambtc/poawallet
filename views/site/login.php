<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

use jambtc\oauthtelegram;
use app\components\Settings;
$checkTelegramAuthorization = Url::to(['oauthtelegram/check-authorization']);


$this->title = 'Login';
?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron pb-5">
          <div class="text-center">
              <img src="css/images/logo.png" alt="" width="220">
          </div>
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

            </div>

        </div>
        <div class="form-row text-center mt-15 mb-5 text-light">
          <a style="color: #dee2e6;" href="<?= Url::to(['site/request-password-reset'])?>" data-loader="show">Forgot password?</a>
        </div>
        <div class="form-divider"></div>
        <div class="form-label-divider"><span>OR</span></div>
        <div class="form-divider"></div>
        <div class="row">
            <div class="col-lg-offset-1 col-lg-11">
                <div class="d-flex flex-column">
            <?php
            $authAuthChoice = yii\authclient\widgets\AuthChoice::begin([
                'baseAuthUrl' => ['site/auth'],
                'popupMode' => false, // don't show the popup window
            ]) ?>
                <?php
                foreach ($authAuthChoice->getClients() as $client) : ?>
                    <li class="auth-clients-holder w-100">
                    <?=
                        $authAuthChoice->clientLink($client,
                            '<span class="btn btn-block btn-primary mb-3">
                                <div class="d-flex flex-row">
                                    <span class="ml-0 mr-4 auth-icon ' . $client->getName() . '"></span>
                                    <span class="block">'.Yii::t('app','Sign with').' '.$client->getTitle() . '</span>
                                </div>
                            </span>',
                            [
                                'class' => ''
                            ]) ?>
                    </li>
                <?php endforeach; ?>

                <?php yii\authclient\widgets\AuthChoice::end() ?>

                <div id="w1" class="auth-clients-holder w-100">
                    <ul class="auth-clients">
                        <li>
                            <a class="telegram auth-link" href="#" title="Telegram">
                                <?php
                                $loginTelegram = new jambtc\oauthtelegram\telegram(
                                    Yii::$app->params['telegram.clientId'],
                                    Yii::$app->params['telegram.clientSecret']);
                                echo $loginTelegram->loginButton($checkTelegramAuthorization,'large');
                                ?>
                            </a>
                        </li>
                    </ul>
                </div>
                </div>
            </div>
        </div>
        <div class="form-mini-divider"></div>




        <div class="form-row txt-center text-light mt-15">
          <?= Yii::t('app','Don\'t you have an account yet?') ?> <a style="color:#007bff;" href="<?php echo Url::to(['site/register']); ?>" data-loader="show"><?= Yii::t('app','Sign Up') ?></a>
        </div>
        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>
