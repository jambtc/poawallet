<?php
use yii\helpers\Html;
// use yii\bootstrap4\ActiveForm;



?>
<?php //$form->errorSummary($sendTokenForm) ?>

<?php $sendTokenForm->from = $fromAddress; ?>

<div class="card bg-transparent no-border">
  <div class="card-body bg-primary">
      <!-- amount to send -->
      <div class="form-group">
          <p class="alert alert-info amount-to-send"></p>
      </div>

      <!-- MESSAGGIO -->
      <div class="form-group">
          <?= $form->field($sendTokenForm, 'memo')->textarea([
              'rows' => 6, 'cols' => 50]) ?>
      </div>

  </div>
  <div class="card-footer">
      <?= Html::submitButton('<i class="fa fa-thumbs-up"></i> '.Yii::t('lang','send'), [
            'class' => 'btn btn-primary float-right pay-submit',
            // 'data-method' => 'post',
            // 'data-pjax' => 1
            // 'data-confirm' => 'Are you sure?'
        ]);
      ?>
  </div>


</div>
