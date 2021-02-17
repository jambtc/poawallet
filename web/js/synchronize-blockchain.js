/**
 * Created by jambtc
 */

var blockchain;


$(function () {
    'use strict';

    blockchain = {
        sync: function(){
            console.log('[blockchain: sync]');

            $.ajax({
                url : 'index.php?r=blockchain/get-blocknumber',
                type: "GET",
                dataType: "json",
                success:function(data)
                {
                    if (data.success)
                    {
                        console.log('[blockchain: sync] difference from blocks:',data.diff);
                        $('.pulse-button').removeClass('pulse-button-offline');
                        if (data.diff > 0){
                            if (data.diff < 2)
                            $('.sync-star').addClass('text-success fa-spin');

                            if (data.diff >=2 && data.diff < 240){
                                $('.sync-difference').html('');
                                $('.blockchain-pairing__loading').remove();
                                $('.sync-star').removeClass('text-success fa-spin');
                            }

                            if (data.diff > 240){ // 1 ora
                                $('.sync-blockchain').html(spinner);
                                $('.sync-difference').html('<small>Synchronizing the blockchain: '+data.diff+' blocks left.</small>');
                            }
        					var post = {
        						id: new Date().toISOString(), // id of indexedDB
        						url		: 'index.php?r=blockchain/check-transactions', // url checkTransactions
                                search_address: data.my_address, // indirizzo da controllare
                                chainBlock: data.chainBlocknumber,
        					};
                            writeData('sync-blockchain', post).then(function() {
                                blockchain.callRegisterSyncBlockchain(data.my_address);
                            });
                        } else {
                            $('.sync-difference').html('');
                            $('.blockchain-pairing__loading').remove();
                            $('.sync-star').removeClass('text-success fa-spin');
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
                            setTimeout(function(){ blockchain.sync() }, 5000);
                        }
                    });
                },
                error: function(j){
                    console.log('[blockchain: sync] ERROR!');
                    $('.pulse-button').addClass('pulse-button-offline');
                    setTimeout(function(){ blockchain.sync(my_address) }, 7000);
                }
            });
        },
    };

    console.log('[blockchain: sync] Avvio...');
    blockchain.sync();



});
