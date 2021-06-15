<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $name;
?>
<div class="container h-100">
  <div class="row h-100 justify-content-center align-items-center">
    <div class="site-error">
      <div class="body-content dash-balance jumbotron pb-5">
        <div class="text-center">
          <img src="css/images/logo.png" alt="" width="220">
        </div>
        <div class="form-divider"></div>
        <h3><?= Html::encode($this->title) ?></h3>

        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <p>
            The above error occurred while the Web server was processing your request.
        </p>
        <p>
            Please contact us if you think this is a server error. Thank you.
        </p>
        <p class="mt-3">
            <a href="<?= Url::to(['site/index']) ?>">
                <button type="button" class="btn btn-warning">Go back!</button>
            </a>
        </p>
    </div>
    </div>
  </div>
</div>
