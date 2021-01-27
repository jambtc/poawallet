<?php
use yii\helpers\Url;

$this->title = Yii::$app->id;
?>
  <div class="site-index">

      <div class="body-content dash-balance">
        <h1  class="txt-white"><?php echo Yii::$app->id; ?></h1>
        <div class="form-divider"></div>
        <p class="txt-white">
          <?php echo Yii::$app->id; ?> –  is a mobile-based website created as a
          wallet to pay for things like paying for internet, electricity, tickets,
          events, charity and much more and others. Built with the Framework7, a
          template with a clean and modern design, and a neat layout.
          Wallet Mobile UI Kit will help you create a prototype for the banking
          application that you are working on and ready for you to use. This UI
          kit which will accelerate and boost your design process and will help
          you develop an outstanding experience. FOO UI Kit is designed according
          to Atomic design base so that all the components are fully customizable.
        </p>
      </div>
      <div class="form-divider"></div>
      <div class="form-row">
        <a href="<?php echo Url::to(['site/login']); ?>" class="button circle block orange">Login</a>
      </div>

      <div class="form-row txt-center mt-15">
        Don't you have an account yet? <a href="<?php echo Url::to(['site/register']); ?>" data-loader="show">Sign Up</a>
      </div>
  </div>

  
