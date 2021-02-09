<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>

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
            'class' => 'btn button circle block blue pay-submit',
        ]);
      ?>
      <a class="pay-close float-right" href="<?= Url::to(['/wallet/index'])?> " style="display: none;"/>
          <button type="button" class="btn button circle block green"><?= Yii::t('lang','Close') ?></button>
      </a>
  </div>


</div>
