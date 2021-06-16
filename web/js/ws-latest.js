/**
 * Created by jambtc
 */

// var blockchain;

$(function () {
    'use strict';

    if (typeof(Worker) !== "undefined") {
        // console.log(`[type of bcWorker]`,typeof(bcWorker));

        if (typeof bcWorker === "undefined") {
            var bcWorker = new Worker("js/web-workers/latestWorker.js");
        }
        bcWorker.onmessage = function(event) {
            console.log('[From bc-latest] data:',event.data);
            var transactions = event.data;
            for (var tx of transactions) {
                console.log('[bc latest] single transaction data:', tx);
                showTransactionRow(tx);
            }

        };
    } else {
        console.log('[bc Main] Sorry, your browser does not support Web Workers');
    }

    function stopBCWorker(){
        bcWorker.terminate();
        bcWorker = undefined;
    }
    // console.log(`[window location]`,window.location.href);

    function showTransactionRow(tx){
    	if ($('tr[data-key="' + tx.id_token + '"]').length){
    		$('tr[data-key="' + tx.id_token + '"]').html(tx.row);
    	} else {
    		$('<tr data-key="' + tx.id_token + '"><td>' + tx.row + '</td></tr>').prependTo(".table-98 > tbody");
    	}
    	$('tr[data-key="' + tx.id_token + '"]').addClass("animationTransaction");
    	console.log('[ws/bc] push options',tx.pushoptions)
    	displayPushNotification(tx.pushoptions);
        $('#total-balance').addClass('animationBalanceIn');
        $('.star-total-balance').addClass('animationStar');
        $('#total-balance').text(tx.balance);
    }

    // avvio la sincronizzazione
    bcWorker.postMessage({
        action : "latest",
    });

});
