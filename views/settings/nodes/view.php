<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Nodes */
// echo '<pre>'.print_r($model,true).'</pre>';
// exit;
$this->title = Yii::t('app', 'View Node');
?>

<div class="dash-balance">
	<div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','Node selected') ?></h3>
	</div>
</div>
<section class="mb-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card ref-card c1">

                <div class="card-body">
                    <div class="table-responsive">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => ['class' => 'table table-sm m-0 table-striped text-light'],
                            'attributes' => [
                                // 'id',
                                'blockchain.denomination',
                                'smartContract.denomination',
                                [
                                    'attribute' => 'smartContract.smart_contract_address',
                                    'format' => 'raw',
                                    'value' => function ($data) {
                                        return Html::tag('p',$data->smartContract->smart_contract_address,
                                            [
                                                'class' => 'text-break',
                                            ]
                                        );

                                    },
                                ],
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
</section>
