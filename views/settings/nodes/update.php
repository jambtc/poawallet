<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Nodes */

$this->title = Yii::t('app', 'Update Node selection');
?>
<div class="h-100 ref-card c7">
	<div class="mt-5">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Node selection') ?></h3>
		</div>
	</div>
	<section class="mb-2">
	    <div class="row">
	        <div class="col-lg-12">
	            <div class="card ref-card c1">
	                <div class="card-body">
	                    <?= $this->render('_form', [
	                        'model' => $model,
	                        ]) ?>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>
</div>
