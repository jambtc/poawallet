<?php
// use yii\helpers\Html;
// use yii\bootstrap4\ActiveForm;



?>
<?php //$form->errorSummary($sendTokenForm) ?>

<?php $sendTokenForm->from = $fromAddress; ?>

<div class="card bg-tra nsparent">
    <div class="card-body">
        <!-- DA -->
        <div class="form-group">
          <?= $form->field($sendTokenForm, 'from')->textInput(['readonly'=>true]) ?>
        </div>
        <!-- A -->
        <?php
        $fieldOptions = [
            'inputTemplate' => '
                <div class="input-group">
                    <span class="input-group-addon"  id="activate-camera-btn">
                        <i class="fa fa-camera"></i>
                    </span>
                    {input}
                </div>'
        ];
          ?>
        <div class="form-group">
            <?= $form->field($sendTokenForm, 'to', $fieldOptions)->textInput(['autofocus' => true, 'validate' ]) ?>
        </div>
        <!-- IMPORTO -->
        <div class="form-group">
             <?= $form->field($sendTokenForm, 'amount')->textInput(['type' => 'number']) ?>
             <?= $form->field($sendTokenForm, 'balance')->hiddenInput(['value' => $balance])->label(false) ?>
        </div>
        <?= $form->errorSummary($sendTokenForm, ['id' => 'error-summary']) ?>
    </div>

</div>
