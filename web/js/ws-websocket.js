var handle;

$(function () {
    'use strict';
    var countError = 0;
    var timeOut = 6000;
    var timeOutError = 30000;

    let wssalert = document.getElementById('wss_server');
    let ethAlert = {
        show: function(){
            wssalert.className = 'show';
        },
        hide: function(){
            $('.pulse-button').removeClass('pulse-button-offline');
            wssalert.className = 'hide';
        }
    }

    if (typeof(Worker) !== "undefined") {
        // console.log(`[type of bcWorker]`,typeof(bcWorker));

        if (typeof w_ethtx === "undefined") {
            var w_ethtx = new Worker("js/web-workers/eth-tx.js");
        }

        w_ethtx.onmessage = function(event) {
            // console.log('[From ethtx] data:',event.data);
            var data = event.data;
            handle.response(data);
        };

        // in caso di errore di connessione o server down riavvio
        w_ethtx.onerror = function(event) {
            $('.pulse-button').addClass('pulse-button-offline');
            console.error("[w_ethtx] WebSocket error observed:", event);
            if (countError >= 6){
                ethAlert.show();
                countError = 0;
            }
            countError ++;
            setTimeout(function(){handle.start()}, timeOut);
        };


    } else {
        console.log('[w_ethtx] Sorry, your browser does not support Web Workers');
    }

    handle = {
        start: function(){
            ethAlert.hide();
            // avvio la sincronizzazione dei messaggi
            w_ethtx.postMessage({
                action : "start",
                user_id : yiiGlobalOptions.cryptedIdUser
            });
        },
        response: function(response){
            if (response.success == true){
                networkDetails(response);

                // analizzare la risposta delle transazioni
                var transactions = response.transactions;
                if (transactions){
                    for (var tx of transactions) {
                        console.log('[w_ethtx] single transaction data:', tx);
                        showTransactionRow(tx);
                    }
                }
                if (response.difference > 0){
                    $('.header-message').html(response.headerMessage);
                } else {
                    $('.header-message').html('');
                }
                setTimeout(function(){handle.start()}, timeOut);
            } else {
                console.log('[w_ethtx] waiting after error: ', timeOutError);
                setTimeout(function(){handle.start()}, timeOutError);
            }
        }
    }
    function showTransactionRow(tx){
        if ($('tr[data-key="' + tx.id_token + '"]').length){
            $('tr[data-key="' + tx.id_token + '"]').html(tx.row);
        } else {
            $('<tr data-key="' + tx.id_token + '"><td>' + tx.row + '</td></tr>').prependTo(".table-98 > tbody");
        }
        $('tr[data-key="' + tx.id_token + '"]').addClass("animationTransaction");
        console.log('[w_ethtx] push options',tx.pushoptions)
        if (Object.keys(tx.pushoptions).length === 0){
            console.log('[w_ethtx] push options è vuoto');
        }else{
            displayPushNotification(tx.pushoptions);
        }
        $('#total-balance').addClass('animationBalanceIn');
        $('.star-total-balance').addClass('animationStar');
        $('#total-balance').text(tx.balance);
    }

    function networkDetails(r){
        if ($('.network-details').length){
            $('.network-block-number').text(r.chainBlocknumber);
            $('.network-block-wallet').text(r.walletBlocknumber);
            $('.network-block-percentage').text(r.percentageCompletion);
            $('.network-block-relativeTime').text(r.relativeTime);

            $('.network-block-hash').html(r.latestBlockHash);
            $('.network-block-wallet-hash').html(r.walletBlockHash);
        }
    }

    handle.start();

});
