/**
 * Created by jambtc
 */

// var blockchain;

$(function () {
    'use strict';

    if (typeof(Worker) !== "undefined") {
        // console.log(`[type of bcWorker]`,typeof(bcWorker));

        if (typeof bcWorker === "undefined") {
            var bcWorker = new Worker("js/web-workers/bcWorker.js");
        }
        bcWorker.onmessage = function(event) {
            // console.log('[From bcWorker] data:',event.data);
            var data = event.data;
            if (data.success)
            {
                console.log('[blockchain: sync] difference from blocks:',data.difference);
                $('.pulse-button').removeClass('pulse-button-offline');
                if (data.difference > 0){
                    if (data.difference > 240){ // 1 ora
                        $('.header-message').html(data.headerMessage);
                    }
                    var postData = {
                        id: new Date().toISOString(), // id of indexedDB
                        //url		: 'index.php?r=blockchain/check-transactions', // url checkTransactions
                        search_address: data.my_address, // indirizzo da controllare
                        chainBlocknumber: data.chainBlocknumber,
                        walletBlocknumber: data.walletBlocknumber,
                    };
                    // writeData('sync-blockchain', post).then(function() {
                        // blockchain.callRegisterSyncBlockchain(data.my_address);
                        bcWorker.postMessage({
                            action : "check-transactions",
                            postData : postData,
                        });
                    // });
                } else {
                    $('.header-message').html('');
                }
            } else {
                $('.pulse-button').addClass('pulse-button-offline');
            }
        };
    } else {
        console.log('[bcMain] Sorry, your browser does not support Web Workers');
    }

    function stopBCWorker(){
        bcWorker.terminate();
        bcWorker = undefined;
    }
    // console.log(`[window location]`,window.location.href);


    // avvio la sincronizzazione
    bcWorker.postMessage({
        action : "sync",
    });

});
