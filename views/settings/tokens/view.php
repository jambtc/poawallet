<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */

$this->title = $model->denomination;
?>
<div class="dash-balance">
    <div class="ref-card c10 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= Yii::t('app','Token details') ?></h3>
		</div>
	</div>
    <section class="trans-sec mt-0 purp" style="padding:15px 0px 0px 0px !important;">
		<div class="card ref-card bg-transparent no-border px-1">
			<div class="card-body">
			<div class="d-flex align-items-center">
                <div class="d-flex flex-grow">
                    <div class="col-lg-12">
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
								'contractType.denomination',
								'decimals',
                                'symbol',
								'blockchain.denomination'
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
			</div>
			<div class="card-footer border-transparent ">
				<div class="d-flex justify-content-between bd-highlight mb-3">
					<div class="p-2 bd-highlight">
						<?= Html::a('<i class="fas fa-trash"></i> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
							'class' => 'btn btn-sm btn-danger',
							'data' => [
								'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
								'method' => 'post',
							],
						]) ?>
					</div>
					<div class="p-2 bd-highlight">
						<?= Html::a('<button type="button" class="btn btn-sm btn-success float-right mr-2">
							<i class="fas fa-edit"></i> '. Yii::t('app', 'Update').'
						</button>', ['update','id' => $model->id]) ?>
						<a class="btn btn-sm btn-primary float-right mr-1" href="<?= Url::to(['settings/nodes/update']) ?>">
							<i class="fas fa-check"></i> <?= Yii::t('app','Select') ?>
						</a>
					</div>
				</div>

			</div>
        </div>
    </section>
</div>

<!-- <div class="dash-balance">
	<div class="ref-card c1 relative">
		<h3 class="w-text">
            <?= Yii::t('app','Token details') ?>
        </h3>
		<a class="btn btn-primary float-right" href="<?= Url::to(['settings/nodes/update']) ?>">
			<i class="fas fa-check"></i> <?= Yii::t('app','Select') ?>
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
								'contractType.denomination',
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
</section> -->
