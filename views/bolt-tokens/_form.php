<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bolt-tokens-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_user')->textInput() ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'token_price')->textInput() ?>

    <?= $form->field($model, 'token_ricevuti')->textInput() ?>

    <?= $form->field($model, 'fiat_price')->textInput() ?>

    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'item_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'item_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'invoice_timestamp')->textInput() ?>

    <?= $form->field($model, 'expiration_timestamp')->textInput() ?>

    <?= $form->field($model, 'rate')->textInput() ?>

    <?= $form->field($model, 'from_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'to_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'blocknumber')->textInput() ?>

    <?= $form->field($model, 'txhash')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
