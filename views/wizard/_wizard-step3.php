<?php
use yii\helpers\Url;
?>
<div class="jumbotron jumbotron-fluid">

    <p class="alert-warning">
        <b>
        <?php echo Yii::t('app','But be careful, keep them in a safe place \'cause anyone who gets hold of this key can access its contents.');?>
        </b>
    </p>
    <p class="lead">
        <?php echo Yii::t('app','Remember to make a backup of your digital wallet. This is an important step in securing your asset.');?>
    </p>
    <div class="form-divider"></div>
    <div class="container">
      <div class="float-left">
        <button type="button" id="stepwizard_step3_prev" class="btn btn-warning btn-md prev-step">
          <i class="fa fa-backward"></i> <?php echo Yii::t('app','Previous');?></button>
      </div>
      <div class="float-right">
        <button type="button" id="stepwizard_step3_next" class="btn btn-primary btn-md next-step">
          <i class="fa fa-forward"></i> <?php echo Yii::t('app','Next');?>
        </button>
      </div>
    </div>
</div>
