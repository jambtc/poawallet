<?php
// use yii\helpers\Html;
// use yii\bootstrap4\ActiveForm;



?>
<?php //$form->errorSummary($sendTokenForm) ?>

<?php $sendTokenForm->from = $fromAddress; ?>

<div class="card bg-primary no-border">
  <div class="card-body">
      <!-- DA -->
      <div class="form-group">
          <?= $form->field($sendTokenForm, 'from')->textInput(['readonly'=>true]) ?>
      </div>
      <!-- A -->
      <div class="form-group">
          <?= $form->field($sendTokenForm, 'to')->textInput(['autofocus' => true, 'validate' ]) ?>

      </div>
      <!-- IMPORTO -->
      <div class="form-group">

         <?= $form->field($sendTokenForm, 'amount')->textInput(['type' => 'number']) ?>
      </div>

  </div>

</div>
