<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('app', 'Update Bolt Tokens: {name}', [
    'name' => $model->id_token,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bolt Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_token, 'url' => ['view', 'id' => $model->id_token]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bolt-tokens-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
