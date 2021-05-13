<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokensSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bolt-tokens-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id_token') ?>

    <?= $form->field($model, 'id_user') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'token_price') ?>

    <?php // echo $form->field($model, 'token_received') ?>

    <?php // echo $form->field($model, 'fiat_price') ?>

    <?php // echo $form->field($model, 'currency') ?>

    <?php // echo $form->field($model, 'item_desc') ?>

    <?php // echo $form->field($model, 'item_code') ?>

    <?php // echo $form->field($model, 'invoice_timestamp') ?>

    <?php // echo $form->field($model, 'expiration_timestamp') ?>

    <?php // echo $form->field($model, 'rate') ?>

    <?php // echo $form->field($model, 'from_address') ?>

    <?php // echo $form->field($model, 'to_address') ?>

    <?php // echo $form->field($model, 'blocknumber') ?>

    <?php // echo $form->field($model, 'txhash') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
