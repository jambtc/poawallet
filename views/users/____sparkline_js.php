<?php

$this->registerJsFile(
    '@web/js/jquery.sparkline.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);

$sparkLine = <<<JS
    $(".accountValue").sparkline([0,13,10,14,15,10,18,15,19], {
            type: 'line',
            width: '100%',
             height: '80',
            barColor: '#72a1ec',
            lineColor: '#6164c1',
            fillColor: 'rgba(97, 100, 193, 0.3)',
            highlightLineColor: 'rgba(0,0,0,.1)',
            highlightSpotColor: 'rgba(0,0,0,.2)'
    });


JS;

$this->registerJs(
    $sparkLine,
    yii\web\View::POS_READY, //POS_END
    'sparkLine'
);
