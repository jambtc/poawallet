/**
 * Created by jambtc
 */

$(function () {
    'use strict';


    /* R E M O V E PIN MANAGE */
    if($('.pincodeslider').length || $('.pincodeslider-remove').length){
        // leggo lo status attuale della tabella pin
        readAllData('pin').then(function(pin){
            if (typeof pin[0] !== 'undefined') {
                $('.pincodeslider').hide();
                $('.pincodeslider-remove').show();
            }
        })


        // intercetta il pulsante Remove PIN e mostra la schermata di inserimento pin
        var pinRemoveCode = document.querySelector('.pincodeslider-remove');
        pinRemoveCode.addEventListener('click', function(){
            event.stopPropagation();
            dropNumpad();
            // readFromId('isPinRequest',1)
            // .then(function(pReq){
            //     var locked = pReq[0].locked;
            //     // writeData('isPinRequest', {'id':1,'value': true,'locked': locked} )
                writeData('isPinRequest', {'id':1,'value': true} )
                .then(function(){
                    $('.pin-remove-numpad').append(pin_ask_numpad);
                    $('.easy-numpad-frame').css("top","1px");
                    readFromId('pin',1)
                    .then(function(pin) {
                        if (typeof pin[0] !== 'undefined') {
                            if (null !== pin[0].id){
                                pincode.ask(pin[0].pin,1);
                            }
                        }
                    });

                })

            // })


        });

        // intercetta il pulsante di annulla RIMOZIONE pin e ripristina lo stato di verifica scadenza pin
        var pinRemoveButtonBack = document.querySelector('#pinRemoveButtonBack');
        pinRemoveButtonBack.addEventListener('click', function(){
            dropNumpad(true);
            // $('#pinRemoveButton').prop('disabled', true);
            pincode.refreshed();
        });

        // intercetta il pulsante di conferma RIMOZIONE pin e lo elimina
        var pinRemoveButton = document.querySelector('#pinRemoveButton');
        pinRemoveButton.addEventListener('click', function(){
            clearAllData('pin')
                .then(function(){
                  location.href = location.href;
                });
        });











    }
});
