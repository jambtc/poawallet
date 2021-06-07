<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\ActiveForm;
use app\components\WebApp;
use app\models\ContractType;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */
/* @var $form yii\widgets\ActiveForm */

$contract_type = ArrayHelper::map(ContractType::find()->all(), 'id', 'denomination');

?>

<div class="smartcontracts-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="txt-left">
        <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12 callout callout-danger']) ?>
    </div>

    <?= $form->field($model, 'denomination')->textInput(['maxlength' => true])->label(Yii::t('app','Denomination')) ?>

    <?= $form->field($model, 'id_contract_type')->dropDownList($contract_type) ?>
    <?= $form->field($model, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

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
