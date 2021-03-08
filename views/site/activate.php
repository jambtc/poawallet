<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;


$this->title = 'Activation';

?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron pb-5">
          <h1 class="alert alert-info"><?= Yii::t('app','Activation form') ?></h1>

          <?php if (Yii::$app->session->hasFlash('dataOutdated')): ?>

              <div class="alert alert-warning">
                  <?php echo Yii::t('app','The registration time has expired.');?><br>
                  <?php echo Yii::t('app','You have to register again.');?>
              </div>
                <div class="form-row txt-center text-light mt-15">
                  <?php echo Yii::t('app','Please, repeat the registration.');?>
                  <a style="color:#007bff;" href="<?php echo Url::to(['site/register']); ?>" data-loader="show">
                      <?php echo Yii::t('app','Sign Up');?>
                  </a>
                </div>
        <?php endif; ?>

        <?php if (Yii::$app->session->hasFlash('dataNotSigned')): ?>
            <div class="alert alert-warning">
                <?php echo Yii::t('app','The registration data isn\'t valid.');?><br>
                <?php echo Yii::t('app','You have to register again.');?>
            </div>
            <div class="form-row txt-center text-light mt-15">
                <?php echo Yii::t('app','Please, repeat the registration.');?>
                <a style="color:#007bff;" href="<?php echo Url::to(['site/register']); ?>" data-loader="show">
                    <?php echo Yii::t('app','Sign Up');?>
                </a>
            </div>

        <?php else: ?>
            <div class="alert alert-success">
                <?php echo Yii::t('app','You have registered your account successfully.');?><br>
            </div>
            <div class="form-row txt-center text-light mt-15">
                <?php echo Yii::t('app','You can now login.');?>
                <a style="color:#007bff;" href="<?php echo Url::to(['site/login']); ?>" data-loader="show">
                    <?php echo Yii::t('app','Sign In');?>
                </a>
            </div>

        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
