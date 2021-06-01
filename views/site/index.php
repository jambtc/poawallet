<?php
use yii\helpers\Url;

$this->title = Yii::$app->id;
?>

<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron  text-center pb-5">
        <img src="css/images/logo.png" alt="" width="220">
        <div class="form-divider"></div>
        <h3  class="txt-white"><?php echo Yii::$app->id; ?></h3>
        <div class="form-divider"></div>

        <h5 class="txt-white">
          <?php echo Yii::$app->id; ?> – <?= Yii::t('app','a Progressive Web App. It works with several crypto tokens and blockchains. With Poa Wallet, you are in control over your funds. Receive, send, store and exchange your cryptocurrency within the mobile interface.') ?>
        </h5>

        <div class="form-divider"></div>

          <div class="form-row">
            <a href="<?php echo Url::to(['site/login']); ?>" class="button circle block orange"><?= Yii::t('app','Start') ?></a>
          </div>
          <div class="form-divider"></div>
          <div class="form-divider"></div>

      </div>
    </div>
  </div>
</div>
