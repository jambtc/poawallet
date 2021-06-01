<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Blockchains */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Blockchains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary px-3">
            <div class="card-header border-transparent ">
                <h3 class="card-title "></h3>
                <?= Html::a('<button type="button" class="btn btn-success float-right">
                    <i class="fas fa-edit"></i> '. Yii::t('app', 'Update').'
                </button>', ['update','id' => $model->id]) ?>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <div class="table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table table-sm m-0 table-striped'],
                    'attributes' => [
                        // 'id',
                        // 'denomination',
                        [
                            'attribute' => 'denomination',
                            'type' => 'raw',
                            'value' => $model->denomination,
                            'contentOptions' => ['style' => 'width:75%;']
                        ],
                        // 'invoice_expiration',
                        // 'smart_contract_address',
                        // 'decimals',
                        'url:url',
                        'chain_id',
                        'url_block_explorer:url',
                        // 'smart_contract_abi',
                        // [
                        //     'attribute' => 'smart_contract_abi',
                        //     'type' => 'raw',
                        //     'value' => $model->smart_contract_abi,
                        //     'contentOptions' => ['class' => 'text-break']
                        // ],
                        // [
                        //     'attribute' => 'smart_contract_bytecode',
                        //     'type' => 'raw',
                        //     'value' => $model->smart_contract_bytecode,
                        //     'contentOptions' => ['class' => 'text-break']
                        // ],
                        // // 'smart_contract_bytecode',
                        // 'sealer_address',
                        // // 'sealer_private_key',
                    ],
                ]) ?>
            </div>
            </div>
        </div>
    </div>
</div>
