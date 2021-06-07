<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Blockchains;
use app\models\SmartContracts;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Nodes */
$blockchains = ArrayHelper::map(Blockchains::find()->all(), 'id', 'denomination');
$smartcontract = ArrayHelper::map(SmartContracts::find()->all(), 'id', 'denomination');

?>

<div class="nodes-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="txt-left">
        <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12 callout callout-danger']) ?>
    </div>

    <?= $form->field($model, 'id_blockchain')->dropDownList($blockchains) ?>
    <?= $form->field($model, 'id_smart_contract')->dropDownList($smartcontract) ?>
    <?= $form->field($model, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
