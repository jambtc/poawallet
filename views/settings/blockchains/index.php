<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BlockchainsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Blockchains');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dash-balance">

    <div class="card">
        <div class="card-header">
            <h3 class="card-title "><?= $this->title ?></h3>
            <?php if ($dataProvider->totalCount == 0): ?>
                <p>
                    <?= Yii::t('app','You must insert a Blockchain node'); ?>
                </p>
            <?php endif ?>
            <?= Html::a('<button type="button" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> '. Yii::t('app', 'Add Blockchain').'
            </button>', ['create']) ?>
        </div>
        <div class="card-body" >
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{summary}\n{items}\n{pager}",
                    'tableOptions' => ['class' => 'table m-0 table-striped'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            // 'id',
                            [
                                'attribute' => 'denomination',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a($data->denomination, Url::toRoute(['view', 'id' => $data->id]));
                                },
                            ],

                            // 'chain_id',
                            // 'url:url',
                            // 'symbol',
                            // 'url_block_explorer:url',


                            // ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>


            </div>

        </div>
        <div class="card-footer text-muted ">

        </div>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary px-3">
            <div class="card-header border-transparent ">

            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">

        </div>
    </div>
</div>
</div>
