<?php
use yii\helpers\Url;
?>
<div class="jumbotron jumbotron-fluid">

    <p class="lead">
        <?php echo Yii::t('lang','These random words will be unique in the world and will allow you to recover the contents of your wallet even in case of loss of your device.'); ?>
    </p>
    <p class="alert-warning">
        <b>
        <?php echo Yii::t('lang','But be careful, keep them in a safe place \'cause anyone who gets hold of this key can access its contents.');?>
        </b>
    </p>
    <hr class="my-4">
    <p class="lead">
        <?php echo Yii::t('lang','Remember to make a backup of your digital wallet. This is an important step in securing your asset.');?>
    </p>

    <button type="button" id="stepwizard_step2_prev" class="btn btn-warning btn-lg prev-step">Previous</button>

    <button type="button" id="stepwizard_step2_next" class="btn btn-primary btn-lg next-step float-right">Next</button>


</div>
