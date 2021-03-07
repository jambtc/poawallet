<?php
use yii\helpers\Url;

$this->title = Yii::$app->id;
?>

<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-login">
      <div class="body-content dash-balance jumbotron  text-center pb-0">
        <img src="css/img/content/onboard2.png" alt="" width="220">
        <div class="form-divider"></div>
        <h3  class="txt-white"><?php echo Yii::$app->id; ?></h3>
        <div class="form-divider"></div>

        <h5 class="txt-white">
          <?php echo Yii::$app->id; ?> –  is a mobile-based website ideated to pay for things like paying for internet, electricity, tickets, events, charity and much more and others. Built with the <span class="txt-orange">Yii2 Framework</span>, a template with a clean and modern design, and a neat layout.
        </h5>

        <div class="form-divider"></div>

          <div class="form-row">
            <a href="<?php echo Url::to(['site/login']); ?>" class="button circle block orange">Login</a>
          </div>
          <div class="form-divider"></div>
          <div class="form-divider"></div>

      </div>
    </div>
  </div>
</div>
