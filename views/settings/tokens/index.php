<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\search\BlockchainsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tokens');
?>
<div class="dash-balance">

    <div class="card vip txt-white">
        <div class="card-header">
            <h3 class="card-title "><?= $this->title ?></h3>
        </div>
        <div class="card-body" >
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{summary}\n{items}\n{pager}",
                    'tableOptions' => ['class' => 'table m-0 table-striped table-sm'],
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            // 'id',
                            [
                                'attribute' => 'denomination',
                                'format' => 'raw',
                                'value' => function ($data) {
                                    return Html::a($data->denomination, Url::toRoute(['view', 'id' => $data->id]),[
                                        'class' => 'txt-white'
                                    ]);
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
            <?= Html::a('<button type="button" class="btn btn-success float-right">
                <i class="fas fa-plus"></i> '. Yii::t('app', 'Add Token').'
            </button>', ['create']) ?>
        </div>
    </div>

</div>
