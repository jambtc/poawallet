<?php
use yii\helpers\Url;
use yii\web\View;


$getUrl = Url::to(['tokens/get-transaction-details','txhash'=>$model->txhash]);

$transget = <<<JS
    $(function(){
        // intercetta il pulsante Remove PIN e mostra la schermata di inserimento pin
        var button = document.querySelector('.trans-get');
        button.addEventListener('click', function(){
            // var that = $(this);

            fetch('{$getUrl}', {
                method: 'GET',
                dataType: "json",
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(json) {
                console.log('[tokens view] receipt', json);
                // var response = JSON.parse(json);

                renewPage();


            });
        });

        function renewPage()
        {
            window.location.href = window.location.href;
            return false;
        }
    });
JS;

$this->registerJs(
    $transget,
    View::POS_READY, //POS_END
    'transget'
);
