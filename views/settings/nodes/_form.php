<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\Blockchains;
use app\models\SmartContracts;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Nodes */

// echo '<pre>'.print_r($model,true).'</pre>';
// exit;

$blockchains = ArrayHelper::map(Blockchains::find()->byUserId($model->id_user)->all(), 'id', 'denomination');

$smartcontract = ArrayHelper::map(SmartContracts::find()
    ->byUserId($model->id_user)
    ->byBlockchainId($model->id_blockchain)
    ->all(), 'id', 'denomination');

include ('_formjs.php');
?>

<div class="nodes-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="txt-left">
        <?php if (Yii::$app->session->hasFlash('nodeSelectedsuccess')): ?>
            <div class="alert alert-success">
                <?php echo  Yii::$app->session->getFlash('nodeSelectedsuccess'); ?><br>
            </div>
        <?php endif; ?>
        <?= $form->errorSummary($model, ['id' => 'error-summary','class'=>'col-lg-12 callout callout-danger']) ?>
    </div>

    <?= $form->field($model, 'id_blockchain')->dropDownList($blockchains) ?>
    <?= $form->field($model, 'id_smart_contract')->dropDownList($smartcontract) ?>
    <?= $form->field($model, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>


    <div class="form-group mt-3">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
