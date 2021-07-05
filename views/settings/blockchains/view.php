<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */

$this->title = $model->denomination;
?>
<div class="dash-balance">
    <div class="ref-card c6 mb-3">
		<div class="dash-content relative">
			<h3 class="w-text"><?= $this->title ?></h3>
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
								'url:url',
								'chain_id',
								'url_block_explorer:url',
							],
						]) ?>
                    </div>
                </div>
            </div>
			</div>
			<div class="card-footer border-transparent ">
				<?= Html::a('<button type="button" class="btn btn-success float-right mr-2">
					<i class="fas fa-edit"></i> '. Yii::t('app', 'Update').'
				</button>', ['update','id' => $model->id]) ?>
			</div>
        </div>
    </section>
</div>

<!-- <div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text">
            <a class="text-light" href="<?= Url::to(['settings/blockchains/index']) ?>">
                <?= Yii::t('app','Blockchain list') ?>
            </a>
        </h3>
	</div>
</div>
<section class="mb-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ref-card c3">
                <div class="card-body">
                    <div class="table-responsive">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table table-sm m-0 table-striped'],
                            'attributes' => [
                                [
                                    'attribute' => 'denomination',
                                    'type' => 'raw',
                                    'value' => $model->denomination,
                                    'contentOptions' => ['style' => 'width:75%;']
                                ],
                                'url:url',
                                'chain_id',
                                'url_block_explorer:url',
                            ],
                        ]) ?>

                    </div>
                </div>
                <div class="card-footer border-transparent ">
                    <?= Html::a('<button type="button" class="btn btn-success float-right mr-2">
                        <i class="fas fa-edit"></i> '. Yii::t('app', 'Update').'
                    </button>', ['update','id' => $model->id]) ?>
                </div>
            </div>
        </div>
    </div>
</section> -->
