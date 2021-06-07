<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\WebApp;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blockchains-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="txt-left">
        <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12 callout callout-danger']) ?>
    </div>

    <?= $form->field($model, 'denomination')->textInput(['maxlength' => true])->label(Yii::t('app','Denomination')) ?>

    <?= $form->field($model, 'url')->textInput() ?>

    <?= $form->field($model, 'chain_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'url_block_explorer')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
