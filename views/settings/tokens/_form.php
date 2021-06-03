<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use app\components\WebApp;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="smartcontracts-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'denomination')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'smart_contract_address')->textInput() ?>

    <?= $form->field($model, 'decimals')->textInput([
                                 'type' => 'number',
                                 'maxlength' => true
                            ]) ?>

    <?= $form->field($model, 'symbol')->textInput(['maxlength' => true]) ?>



    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
