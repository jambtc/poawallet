<?php
$urlRescan = yii\helpers\Url::to(['wallet/rescan']);


$blockchainScan = <<<JS

$(function () {
    'use strict';

    // check click su slider
    var blockchainRescanSlider = document.querySelector('.blockchainRescan');
    blockchainRescanSlider.addEventListener('click', function(){
        wssstop = 1;
        webSocket.close();
        console.log('[ws] websocket close', wssstop);
        $('#blockchainScanModal').modal({
			  backdrop: 'static',
			  keyboard: false
		});

    });

    function resetSlider(className)
    {
        wssstop = 0;
        $('.'+className).removeClass('checked');
    }


    // INTERCETTA IL PULSANTE indietro e reset pulsanti
    var blockchainScanSliderBack2 = document.querySelector('.blockchainScanSliderBack2');
    blockchainScanSliderBack2.addEventListener('click', function(){
        resetSlider('blockchainRescan');
    });

    // INTERCETTA IL PULSANTE indietro e reset pulsanti
    var resetSliderAria = document.querySelector('#resetSliderAriablockchain');
    resetSliderAria.addEventListener('click', function(){
        resetSlider('blockchainRescan');
    });


    // read seed from dbx and decrypt
    var showblockchainScan = document.querySelector('#showblockchainScan');
	showblockchainScan.addEventListener('click', function() {
        $.ajax({
            url:'{$urlRescan}',
            type: "POST",
            dataType: "json",
            success:function(data){
                resetSlider('blockchainRescan');
            },

        });
	});
});






JS;
$this->registerJs(
    $blockchainScan,
    yii\web\View::POS_READY, //POS_END
    'blockchainScan'
);

?>
