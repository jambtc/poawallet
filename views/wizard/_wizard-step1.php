<?php
use yii\helpers\Url;
?>
<div class="jumbotron jumbotron-fluid">
    <h3 class="display-4"><?php echo Yii::t('app','Hi').' <span class="text-capitalize">'.Yii::$app->user->identity->first_name.'</span>,';?></h3>
    <p class="lead">
        <?= Yii::t('app','soon you will activate your new wallet.'); ?>
    </p>
    <p>
        <?= Yii::t('app','Your electronic wallet will be made secure thanks to a mathematical process that will make the content unreadable.'); ?>
    </p>


    <div class="form-divider"></div>
    <div class="container">
      <div class="float-left">
        <form action="<?php echo Url::to(['/site/logout']); ?>" method="POST" id="form-logout">
            <a href="#">
                <button class="btn btn-warning btn-md site-logout">
                    <i class="fas fa-sign-out-alt"></i> <?php echo Yii::t('app','Logout');?></button>
            </a>
        </form>
      </div>
      <div class="float-right">
        <button type="button" id="stepwizard_step1_next" class="btn btn-primary btn-md next-step" >
          <i class="fa fa-forward"></i> <?php echo Yii::t('app','Start');?>
        </button>
      </div>
    </div>


</div>
