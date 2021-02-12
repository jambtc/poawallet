<?php

$this->registerJsFile(
    '@web/js/jquery.sparkline.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$sparkLine = <<<JS
    $(".profile3").sparkline([0.1,5,-6,-6,9,5,75,-7.02,8,5,6,8 ], {
            type: 'bar',
            width: '100%',
            height: '50',
            barColor: '#72a1ec'
    });

JS;

$this->registerJs(
    $sparkLine,
    yii\web\View::POS_READY, //POS_END
    'sparkLine'
);
