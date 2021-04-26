<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BoltTokensSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Messages');
$deleteUrl = yii\helpers\Url::to(['/messages/delete']);
$deleteMessage = Yii::t('app','Are you sure you want to delete selected items?');

$deleting = <<<JS

    $(function(){
        // intercetta il pulsante Remove PIN e mostra la schermata di inserimento pin
        if ($('.btn-delete').length){
            var deleteNotificationButton = document.querySelector('.btn-delete');
            deleteNotificationButton.addEventListener('click', function(){
                if (confirm('{$deleteMessage}')) {
                    var keys = $('#notifications-form').yiiGridView('getSelectedRows');
                    console.log('[delete] valori selezionati:',keys);
                    $.ajax({
                        url: '{$deleteUrl}',
                        data: {
                            keys: JSON.stringify(keys),
                        },
                        type: "POST",
                        success: function(result) {
                            // reload page from redirect
                        }
                    });
                }
            });
        }
    });
JS;

$this->registerJs(
    $deleting,
    yii\web\View::POS_READY, //POS_END
    'deleting'
);
?>
<!-- <div class="form-divider"></div> -->
<div class="dash-balance ">
    <div class="dash-content relative">
		<h3 class="w-text"><?= Yii::t('app','Notifications list');?></h3>
	</div>
    <p class="float-right">
        <?php
        if ($dataProvider->totalCount >0) { ?>
            <?= Html::button(Yii::t('app', 'Delete'), [
                'class' => 'btn btn-danger btn-delete',
            ]) ?>
        <?php } ?>

    </p>

    <section class="mb-2">


        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'id' => 'notifications-form',
            // 'filterModel' => $searchModel,
            // 'showHeader'=> false,
            'tableOptions' => ['class' => 'table-96 table table-sm mb-3 ml-1 mr-1'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'id',
                    // 'header' => 'Select all',
                ],
                [
                   'attribute'=>'',
                   'format' => 'raw',
                   'class' => 'yii\grid\DataColumn', // can be omitted, as it is the default
                   'value' => function ($data) {
                      return app\components\WebApp::showMessagesRow($data);
                   },
                ],
            ],
        ]); ?>

        <div class="form-divider"></div>
        <div class="form-divider"></div>
    </section>


</div>
