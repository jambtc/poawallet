<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */

$this->title = $model->denomination;
?>
<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text">
            <?= Yii::t('app','Token details') ?>
        </h3>
		<a class="text-light float-right" href="<?= Url::to(['settings/tokens/index']) ?>">
			<?= Yii::t('app','back') ?>
		</a>
	</div>
</div>
<section class="mb-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ref-card c2">

                <div class="card-body">
                    <div class="table-responsive">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table table-sm m-0 table-striped text-light'],
                            'attributes' => [
                                [
                                    'attribute' => 'denomination',
                                    'type' => 'raw',
                                    'value' => $model->denomination,
                                    'contentOptions' => ['style' => 'width:75%;']
                                ],
								[
                                    'attribute' => 'smart_contract_address',
                                    'type' => 'raw',
                                    'value' => $model->smart_contract_address,
                                    'contentOptions' => ['class' => 'text-break']
                                ],
								'decimals',
                                'symbol',
								'blockchain.denomination'
                            ],
                        ]) ?>

                    </div>
                </div>
                <div class="card-footer border-transparent ">
                    <?= Html::a('<button type="button" class="btn btn-success float-right mr-2">
                        <i class="fas fa-edit"></i> '. Yii::t('app', 'Update').'
                    </button>', ['update','id' => $model->id]) ?>
					<?= Html::a('<i class="fas fa-trash"></i> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</section>
