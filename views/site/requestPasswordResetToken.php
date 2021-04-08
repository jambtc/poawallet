<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Request password reset';
?>
<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
        <div class="site-login">
            <div class="body-content dash-balance jumbotron pb-5">
                <div class="text-center">
                    <img src="css/images/logo.png" alt="" width="220">
                </div>
                <div class="form-divider"></div>
                <h3 class="alert alert-info"><?= Yii::t('app','Request password reset') ?></h3>
                <p class="text-light"><?= Yii::t('app','Please fill out your email. A link to reset password will be sent there.') ?></p>
                <div class="row">
                    <div class="col-lg-5">
                        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
                        <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>
                        <div class="txt-left">
                            <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12']) ?>
                        </div>
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app','Send'), ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
