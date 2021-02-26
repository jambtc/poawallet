

$(function () {

    startWebSocket();

    console.log ( '[ws] datakey', $('tr[data-key="' + 105 + '"]').length          );

    function startWebSocket(){
        var webSocket = new WebSocket('ws://localhost:8081');

        // all'apertura leggi il numero di blocchi
        webSocket.onopen = function(e) {
            console.log('[ws] onopen user_id:', yiiGlobalOptions.cryptedIdUser);
            webSocket.send( JSON.stringify({
                'action' : 'setUserId',
                'user_id' : yiiGlobalOptions.cryptedIdUser
            }));
        };

        // in caso di errore di connessione o server down riavvio
        webSocket.onerror = function(event) {
            console.error("[ws] WebSocket error observed:", event);
            $('.pulse-button').addClass('pulse-button-offline');
            // trying to restart
            setTimeout(function(){ startWebSocket() }, 10000);
        };

        // gestico i messaggi di risposta dal server
        webSocket.onmessage = function(e) {
            var response = JSON.parse(e.data);
            // console.log('[ws] Response:' + e.data);
            // console.log('[ws] Response success:' + response.success);
            // console.log('[ws] Message:' + response.message);

            // risposta di leggi il block number
            // risposta di check transactions
            if (response.success == true){
                // console.log('[ws] response from getBlockNumber');
                // console.log('[ws] from getBlockNumber] blocks difference:',response.difference);
                $('.pulse-button').removeClass('pulse-button-offline');

                // analizzare la risposta delle transazioni
                var transactions = response.transactions;
                console.log('[ws] Transactions are: ', transactions);
                if (transactions){
                    for (var tx of transactions) {
                        console.log('[ws] single transaction data:', tx);
                        showTransactionRow(tx);
                    }
                    // chiamapippo();
                }

                if (response.difference > 0){
                    $('.header-message').html(response.headerMessage);
                    var postData = {
                        search_address: response.user_address, // indirizzo da controllare
                        chainBlocknumber: response.chainBlocknumber,
                        walletBlocknumber: response.walletBlocknumber,
                    };
                    // console.log('[ws: getBlockNumber] postData:',postData);
                    webSocket.send( JSON.stringify({
                        'action' : 'checkTransactions',
                        'postData' : postData,
                    }));
                } else {
                    $('.header-message').html('');
                    postData.chainBlocknumber += 10;
                    setTimeout(function(){
                        webSocket.send( JSON.stringify({
                            'action' : 'checkTransactions',
                            'postData' : postData,
                        }));
                    }, 10000);
                }
            }
        };
    }

    function showTransactionRow(tx){

        if ($('tr[data-key="' + tx.id_token + '"]').length){
            $('tr[data-key="' + tx.id_token + '"]').html(tx.row);
        } else {
            $('<tr data-key="' + tx.id_token + '"><td>' + tx.row + '</td></tr>').prependTo(".table-98 > tbody");
        }
        $('tr[data-key="' + tx.id_token + '"]').addClass("animationTransaction");
        console.log('[ws] push options',tx.pushoptions)
        displayPushNotification(tx.pushoptions);
    }
});
