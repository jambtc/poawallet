<?php


$managePin = <<<JS

$(function () {
    'use strict';


    // leggo lo status attuale della tabella pin
    readAllData('pin').then(function(pin){
        if (typeof pin[0] !== 'undefined') {
            $('.pincodeslider').hide();
            $('.pincodeslider-remove').show();
            $('.masterSeedMessagePinEnabled').show();
        }else{
            $('.masterSeedMessagePinDisabled').show();
        }
    })

    var pinNewCodeSlider = document.querySelector('.pincodeslider');
    pinNewCodeSlider.addEventListener('click', function(){
        $('.pin-numpad').append(pin_numpad);
        $('.easy-numpad-frame').css("top","1px");

        $('#pinNewModal').modal({
			  backdrop: 'static',
			  keyboard: false
		 });

    });

    // INTERCETTA IL PULSANTE indietro SULLA PRIMA SCHERMATA e reset pulsanti
    var pinNewButtonBack = document.querySelector('#pinNewButtonBack');
    pinNewButtonBack.addEventListener('click', function(){
        $('.pincodeslider').removeClass('checked');
        dropNumpad(true);
        $('#pinNewButton').prop('disabled', true);
    });

    // INTERCETTA IL PULSANTE DI CONFERMA SULLA PRIMA SCHERMATA E MOSTRA QUELLA DI VERIFICA DEL PIN
    var pinNewButton = document.querySelector('#pinNewButton');
    pinNewButton.addEventListener('click', function(){
        dropNumpad();
        $('#pinNewModal').modal("hide");
        $('.pin-confirm-numpad').append(pin_confirm_numpad);
        $('.easy-numpad-frame').css("top","1px");

        $('#pinVerifyButton').prop('disabled', true);
        $('#pinVerifyButton').addClass('disabled');

        $('#pinVerifyModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });

    // INTERCETTA IL PULSANTE indietro SULLA seconda SCHERMATA e reset pulsanti
    var pinVerifyButtonBack = document.querySelector('#pinVerifyButtonBack');
    pinVerifyButtonBack.addEventListener('click', function(){
        $('.pincodeslider').removeClass('checked');
        dropNumpad(true);
        $('#pinNewButton').prop('disabled', true);
        $('#pinVerifyButton').prop('disabled', true);
    });

    // intercetta il pulsante di conferma VERIFICA Pin sulla seconda schermata e salva il PIN
    var pinVerifyButton = document.querySelector('#pinVerifyButton');
    pinVerifyButton.addEventListener('click', function(){
        //cripta il pin
        $.ajax({
    		url: yiiOptions.cryptURL,
    		type: "POST",
    		data: { 'pass': $('#pin_password').val() },
    		dataType: "json",
            beforeSend: function(){
                $('#pinVerifyButtonText').hide();
                $('#pinVerifyButtonText').after(spinner);
            },
            success:function(data){
                var savePin = {
        			id		: 1, // new Date().getTime() /1000 | 0, // timestamp
                    time    : new Date().getTime() /1000 | 0,
                    stop	: yiiOptions.expiringTime,
                    pin     : data.cryptedpass,
                };
                // svuoto la tabella
                clearAllData('pin').then(function(){
                    writeData('pin', savePin).then(function() {
                        console.log('Saved pin info in indexedDB', savePin);
                        setTimeout(function(){
                            location.href = location.href
                        }, 500);
                    })
                    .catch(function(err) {
                        console.log(err);
                    });
                });
            },
            error: function(j){
                console.log('error',j);
            }
        });
    });




});






JS;
$this->registerJs(
    $managePin,
    yii\web\View::POS_READY, //POS_END
    'managePin'
);

?>
