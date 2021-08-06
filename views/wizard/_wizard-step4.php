<?php
use yii\helpers\Url;
use yii\helpers\Html;

// make restore session id
$session = Yii::$app->session;
$string = Yii::$app->security->generateRandomString(32);
$session->set('token-restore', $string );
$session->set('token-spawn', $string );

?>
<div class="jumbotron jumbotron-fluid">

    <p class="lead">
        <?php echo Yii::t('app','If you already have a mnemonic key (seed) and want to restore your old wallet, press the button <i><b>"Restore"</b></i>');?>
    </p>

    <hr class="my-4">
    <p class="lead">
        <?php echo Yii::t('app','If you want to generate a new digital wallet, click on the <i><b>"New keys"</b> </i> button.');?>
    </p>
    <div class="form-divider"></div>

    <button type="button" id="stepwizard_step4_prev" class="btn btn-warning btn-md prev-step">
    <i class="fa fa-backward"></i> <?php echo Yii::t('app','Previous');?>
    </button>

    <div class="float-right">
        <a href="<?php echo Url::to(['/restore/index','token' => $string]) ?>" />
            <button type="button"  class="btn btn-primary btn-md " >
                <i class="glyphicon glyphicon-repeat"></i> <?php echo Yii::t('app','Restore');?>
            </button>
        </a>
        <!-- <a href="<?php echo Url::to(['/spawn/index','token' => $string]) ?>" />
            <button type="button"  class="btn btn-primary btn-md" >
                <i class="fas fa-key"></i> <?php echo Yii::t('app','New');?>
            </button>
        </a> -->

        <div class="float-right">
                  <?= Html::Button('<i class="fas fa-key"></i> <span id="js-newseed-btn-text">'.Yii::t('app','New keys').'</span>', [
                  'class' => 'btn btn-success btn-md seed-submit ml-2',
                  'id' => 'seed-submit',
                  'data-method' => 'post',
              ]);
            ?>
        </div>


    </div>

</div>
