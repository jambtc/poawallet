/**
 * Created by jambtc
 */

var pincode;

$(function () {
    'use strict';

    pincode = {
        refreshed: function(){
            readAllData('isPinLocked')
            .then(function(ipl){
                if (typeof ipl[0] === 'undefined'){
                    writeData('isPinLocked', {'id':1,'value': false} );
                }
            })
            writeData('isPinRequest', {'id':1,'value': false} );

            readAllData('pin')
            .then(function(pin){
                if (typeof pin[0] !== 'undefined'){
                    if (pin[0].id == 1){
                        var post = {
                			id		: 1, // new Date().getTime() /1000 | 0, // timestamp
                            time    : new Date().getTime() /1000 | 0,
                            stop	: pin[0].stop,
                            pin     : pin[0].pin,
                        };
                        // aggiorno il nuovo timestamp in tabella
                        writeData('pin', post)
                        .then(function(){
    	 					post.scadenza = post.time + (pin[0].stop * 60);
                            pincode.check(post);
                        })

                    } else {
                        console.log('[backend: checkPin] Dati tabella pin non coerenti!');
                    }
                } else {
                    console.log('[backend: checkPin] Nessun pin impostato!');
                }
            })
        },
    	check: function(data){
            console.log('[Checktime for pin] data: ',data);
    		var adesso = new Date().getTime() /1000 | 0;
    		var differenza = data.scadenza - adesso;

            console.log('[Checktime for pin] difference: ',differenza);

            readFromId('isPinRequest',1)
            .then( function (ipr) {
                console.log('[Pin] isPinRequest value:', ipr);
                readFromId('isPinLocked',1)
                .then( function(ipl) {
                    if ((differenza <= 0 && ipr[0].value == false)
                        || (ipr[0].value == false && ipl[0].value == true)
                    ){
                        writeData('isPinRequest', {'id':1,'value': true} )
                        .then(function(){
                            writeData('isPinLocked', {'id':1,'value': true} )
                            .then(function(){
                                pincode.ask(data.pin);
                            })

                        })
                    }else{
                        if (differenza <= 0){
                            writeData('isPinLocked', {'id':1,'value': true} )
                        }
                        setTimeout(function(){
                            console.log('[Pin] ricarica...');
                            pincode.check(data);
                        }, 5000);

                    }




                })






            })

    	},
        ask: function (crypted_pin,mask=0) {
            //return new Promise(function(resolve, reject){
        		$.ajax({
        			url:'index.php?r=wallet/decrypt',
        			type: "POST",
        			data: {'pass': crypted_pin},
        			dataType: "json",
        			success:function(data){
        				console.log('[Pin Utility] decrypted pin code',data);
        				$('#pin_password').val(data.decrypted);
        				$('.pin-confirm-numpad').append(pin_ask_numpad);
        				$('.easy-numpad-frame').css("top","1px");

        				if (mask==0){
                            //writeData('isPinRequest', {'id':1,'value': false, 'locked': true} );
        					$('#pinRequestModal').modal({
        						backdrop: 'static',
        						keyboard: false
        					});
        				}else{
        					$('#pinRemoveModal').modal({
        				      backdrop: 'static',
        				      keyboard: false
        				    });
        				}
        			},
        			error: function(j){
        				console.log('error');
        			}
        		});
            //});
        }

    };


    /** aggiorno il timestamp del pin su richiesta*/
    function updatePinTimestamp(){
    	dropNumpad(true);
        writeData('isPinLocked', {'id':1,'value': false} );

    	readAllData('pin')
    		.then(function(old) {
    			var post = {
                    id      : 1, //
    				time	: new Date().getTime() /1000 | 0, // timestamp
    				stop	: old[0].stop,
    				pin     : old[0].pin,
    			};
    			writeData('pin', post)
    				.then(function() {
    					console.log('[Pin Utility] Update pin info in indexedDB', post);
    					$('#pinRequestModal').modal("hide");
    					$('#pinRequestButtonText').show();
                        $('#pinRequestButton').prop('disabled',true);
                        $('.button-spinner').remove();

                        pincode.refreshed();
    				})
    		})
    }

    if($('#pinRequestButton').length){
        //controllo la pressione del pulsante conferma PIN ed aggiorno il timestamp
        var pinRequestButton = document.querySelector('#pinRequestButton');
        pinRequestButton.addEventListener('click', function(){
            $('#pinRequestButtonText').hide();
            $('#pinRequestButtonText').after(spinner);
            // writeData('isPinRequest', {'id':1,'value': false} );
            updatePinTimestamp();
        });



        // chiamo la funzione al refresh della pagina
        pincode.refreshed();
    }
});
