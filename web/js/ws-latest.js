/**
 * Created by jambtc
 */

// var blockchain;

$(function () {
    'use strict';
    var timeOut = 10000;

    // funzione che legge i pending per verificare i pagamenti
    checkPendingTransactions();

    if (typeof(Worker) !== "undefined") {
        // console.log(`[type of bcWorker]`,typeof(bcWorker));

        if (typeof bcWorker === "undefined") {
            var bcWorker = new Worker("js/web-workers/latestWorker.js");
        }
        bcWorker.onmessage = function(event) {
            // console.log('[From bc-latest] data:',event.data);
            var transactions = event.data;
            for (var tx of transactions) {
                console.log('[bc latest] single transaction data:', tx);
                showTransactionRow(tx);
            }
            setTimeout(function(){bcWorker.postMessage({
                    action : "latest",
                })
            }, timeOut);

        };
    } else {
        console.log('[bc Main] Sorry, your browser does not support Web Workers');
    }

    function stopBCWorker(){
        bcWorker.terminate();
        bcWorker = undefined;
    }
    // console.log(`[window location]`,window.location.href);


    // avvio la sincronizzazione
    bcWorker.postMessage({
        action : "latest",
    });

});
