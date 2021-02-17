<?php
use yii\helpers\Url;
// make restore session id
$session = Yii::$app->session;
$string = Yii::$app->security->generateRandomString(32);
$session->set('token-restore', $string );

?>
<div class="jumbotron jumbotron-fluid">

    <p class="lead">
        <?php echo Yii::t('lang','If you already have a mnemonic key (seed) and want to restore your old wallet, press the button <i><b>"Restore"</b></i>');?>
    </p>

    <hr class="my-4">
    <p class="lead">
        <?php echo Yii::t('lang','If you want to generate a new digital wallet, click on the <i><b>"New"</b> </i> button and follow the recommended instructions.');?>
    </p>

    <button type="button" id="stepwizard_step3_prev" class="btn btn-warning btn-lg prev-step">Previous</button>

    <div class="float-right">
        <a href="<?php echo Url::to(['/restore/index','token' => $string]) ?>" />
            <button type="button"  class="btn btn-primary btn-lg " >
                <i class="fas fa-repeat"></i> <?php echo Yii::t('lang','Restore');?>
            </button>
        </a>
        <a href="<?php echo Url::to(['/new/index']) ?>" />
            <button type="button"  class="btn btn-primary btn-lg" >
                <i class="fas fa-key"></i> <?php echo Yii::t('lang','New');?>
            </button>
        </a>
    </div>

</div>
