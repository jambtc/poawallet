<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Reset password';
?>
<div class="container h-100">
    <div cl ass="row h-100 justify-content-center align-items-center">
        <div class="site-login">
            <div class="body-content dash-balance jumbotron pb-5">
                <div class="text-center">
                    <img src="css/images/logo.png" alt="" width="220">
                </div>
                <div class="form-divider"></div>
                <h3 class="alert alert-info"><?= Yii::t('app','Reset password') ?></h3>
                <p class="text-light"><?= Yii::t('app','Please choose your new password:') ?></p>
                <div class="row">
                    <div class="col-lg-5">
                        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>
                        <?= $form->field($model, 'password')->passwordInput(['autofocus' => true]) ?>
                        <div class="txt-left">
                            <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
                        </div>
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app','Save'), ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
