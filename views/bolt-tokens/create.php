<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\BoltTokens */

$this->title = Yii::t('app', 'Create Bolt Tokens');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bolt Tokens'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bolt-tokens-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
