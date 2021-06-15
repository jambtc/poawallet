<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */

$this->title = Yii::t('app', 'Create Network');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Networks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dash-balance">
    <div class="ref-card c1 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Insert Network');?></h3>
		</div>
	</div>
    <section class="trans-sec mt-0 purp" style="padding:15px 0px 0px 0px !important;">

		<div class="ref-card ">
			<div class="d-flex align-items-center">
                <div class="d-flex flex-grow">
                    <div class="col-lg-12">

                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
