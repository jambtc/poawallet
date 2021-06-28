var ethtxs;

$(function () {
    'use strict';

    if (typeof(Worker) !== "undefined") {
        // console.log(`[type of bcWorker]`,typeof(bcWorker));

        if (typeof w_ethtx === "undefined") {
            var w_ethtx = new Worker("js/web-workers/eth-tx.js");
        }
        w_ethtx.onopen = function(e) {
            // $('.pulse-button').addClass('pulse-button-offline');
            hideWssAlert();
            console.log('[w_ethtx] onopen user_id:', yiiGlobalOptions.cryptedIdUser);
        };
        
        w_ethtx.onmessage = function(event) {
            console.log('[From w_ethtx] data:',event.data);
            var data = event.data;
            ethtxs.handleResponse(data);
        };
        // in caso di errore di connessione o server down riavvio
        w_ethtx.onerror = function(event) {
            console.error("[w_ethtx] WebSocket error observed:", event);

            if (countError >= 6){
                showWssAlert();
                countError = 0;
            }
            countError ++;

            // trying to restart
            setTimeout(function(){
                w_ethtx.postMessage({
                    action : "start",
                });
            }, 10000);
        };


    } else {
        console.log('[w_ethtx] Sorry, your browser does not support Web Workers');
    }

    ethtxs = {
        handleResponse: function(response)
        {



        }
    }


    // avvio la sincronizzazione dei messaggi
    w_ethtx.postMessage({
        action : "start",
    });


});
