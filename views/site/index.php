<?php
use yii\helpers\Url;

$this->title = Yii::$app->id;
?>


  <div class="site-login">

      <div class="body-content dash-balance jumbotron  text-center pb-0">
        <h2  class="txt-white"><?php echo Yii::$app->id; ?></h2>
        <div class="form-divider"></div>
        <h5 class="txt-white">
          <?php echo Yii::$app->id; ?> –  is a mobile-based website created as a wallet to pay for things like paying for internet, electricity, tickets, events, charity and much more and others. Built with the <span class="txt-orange">Yii2 Framework</span>, a template with a clean and modern design, and a neat layout.
        </h5>

        <div class="form-divider"></div>
        <div class="form-divider"></div>
        <div class="form-divider"></div>
        <section class="trans-sec container">

          <div class="form-row">
            <a href="<?php echo Url::to(['site/login']); ?>" class="button circle block orange">Login</a>
          </div>

          <div class="form-row txt-center mt-15">
            Don't you have an account yet? <a href="<?php echo Url::to(['site/register']); ?>" data-loader="show">Sign Up</a>
          </div>
        </section>


      </div>

  </div>
