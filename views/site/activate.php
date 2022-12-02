<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\View;


$this->title = 'Activation';

?>
<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
        <div class="site-login">
            <div class="body-content dash-balance jumbotron pb-5">
                <div class="text-center">
                    <img src="css/images/logo.png" alt="" width="220">
                </div>
                <div class="form-divider"></div>
                <h3 class="alert alert-info"><?= Yii::t('app', 'Activation form') ?></h3>

                <?php if (Yii::$app->session->hasFlash('userActived')) : ?>
                    <div class="card-body login-card-body">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <?php echo Yii::$app->session->getFlash('userActived'); ?>
                            <a class="mt-3 btn btn-primary btn-block" href="<?php echo Url::to(['site/login']); ?>" data-loader="show">
                                <?php echo Yii::t('app', 'Login'); ?>
                            </a>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('registerError')) : ?>
                    <div class="alert alert-warning">
                        <?php echo Yii::$app->session->getFlash('registerError'); ?><br>

                        <a class="mt-3 btn btn-primary btn-block" href="<?php echo Url::to(['site/index']); ?>" data-loader="show">
                            <?php echo Yii::t('app', 'Back to home!'); ?>
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>