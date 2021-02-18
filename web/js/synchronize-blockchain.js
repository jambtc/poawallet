/**
 * Created by jambtc
 */

var blockchain;


$(function () {
    'use strict';

    blockchain = {
        sync: function(){
            console.log('[blockchain: sync] Start process');

            $.ajax({
                url : 'index.php?r=blockchain/get-blocknumber',
                type: "GET",
                dataType: "json",
                success:function(data)
                {
                    if (data.success)
                    {
                        console.log('[blockchain: sync] difference from blocks:',data.difference);
                        $('.pulse-button').removeClass('pulse-button-offline');
                        if (data.difference > 0){
                            if (data.difference < 2)
                                $('.sync-star').addClass('text-success fa-spin');

                            if (data.difference >=2 && data.difference < 240){
                                // $('.sync-difference').html('');
                                // $('.blockchain-pairing__loading').remove();
                                $('.sync-star').removeClass('text-success fa-spin');
                            }

                            if (data.difference > 240){ // 1 ora
                                //$('.sync-blockchain').html(spinner);
                                $('.header-message').html('<small title="'+data.relativeTime+'">'+data.diff+'</small>');
                            }
        					var post = {
        						id: new Date().toISOString(), // id of indexedDB
        						url		: 'index.php?r=blockchain/check-transactions', // url checkTransactions
                                search_address: data.my_address, // indirizzo da controllare
                                chainBlocknumber: data.chainBlocknumber,
                                walletBlocknumber: data.walletBlocknumber,
        					};
                            writeData('sync-blockchain', post).then(function() {
                                blockchain.callRegisterSyncBlockchain(data.my_address);
                            });
                        } else {
                            $('.header-message').html('');
                            // $('.blockchain-pairing__loading').remove();
                            // $('.sync-star').removeClass('text-success fa-spin');
                        }
                    } else {
                        $('.pulse-button').addClass('pulse-button-offline');
                    }

                    // leggo adesso np-transaction
                    // Se è vuoto torno subito a sync
                    // se è pieno proseguo...
                    readAllData('np-transactions').then(function(data) {
                        if (typeof data[0] !== 'undefined') {
                            blockchain.readTransactions(data.my_address);
                        }else{
                            setTimeout(function(){ blockchain.sync() }, 1500);
                        }
                    });
                },
                error: function(j){
                    var seconds = 10;
                    console.log('[blockchain: sync] ERROR! Restart in '+seconds+' seconds.');
                    $('.pulse-button').addClass('pulse-button-offline');
                    setTimeout(function(){ blockchain.sync() }, seconds*1000);
                }
            });
        },
        callRegisterSyncBlockchain: function (){
            readAllData('sync-blockchain').then(function(data) {
                navigator.serviceWorker.ready.then(function(sw) {
                    console.log('[blockchain: sync] event register:', data);
                    return sw.sync.register('sync-blockchain');
                });
            })
        },

    };

    console.log('[blockchain: sync] Avvio...');
    blockchain.sync();



});
