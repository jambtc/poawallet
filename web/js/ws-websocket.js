$(function () {
    var countError = 6;
    startWebSocket();

    function startWebSocket(){
        var webSocket = new WebSocket(yiiGlobalOptions.WebSocketServerAddress);

        // console.log('[ws] readyState:', readyState);
        // all'apertura leggi il numero di blocchi
        webSocket.onopen = function(e) {
            console.log('[ws] onopen user_id:', yiiGlobalOptions.cryptedIdUser);
            webSocket.send( JSON.stringify({
                'action' : 'setUserId',
                'user_id' : yiiGlobalOptions.cryptedIdUser
            }));
            getWsState(webSocket);
        };

        // in caso di errore di connessione o server down riavvio
        webSocket.onerror = function(event) {
            console.error("[ws] WebSocket error observed:", event);
            $('.pulse-button').addClass('pulse-button-offline');
            if (countError >= 6){
                showWssAlert();
                countError = 0;
            }
            countError ++;

            // trying to restart
            setTimeout(function(){ startWebSocket() }, 10000);
        };

        // gestico i messaggi di risposta dal server
        webSocket.onmessage = function(e) {
            var response = JSON.parse(e.data);

            if (response.success == true){
                $('.pulse-button').removeClass('pulse-button-offline');

                // analizzare la risposta delle transazioni
                var transactions = response.transactions;
                console.log('[ws] Old Transactions are: ', transactions);
                if (transactions){
                    for (var tx of transactions) {
                        console.log('[ws] Old single transaction data:', tx);
                        showTransactionRow(tx);
                    }
                }
                var postData = {
                    search_address: response.user_address, // indirizzo da controllare
                    chainBlocknumber: response.chainBlocknumber,
                    walletBlocknumber: response.walletBlocknumber,
                };
                var timeOut = 0;

                console.log('[ws] Old difference:', response.difference);

                if (response.difference > 0){
                    $('.header-message').html(response.headerMessage);

                } else {
                    $('.header-message').html('');
                    var hexWallet = (parseInt(postData.walletBlocknumber, 16) - 0x00000a).toString(16);
                    var hexChain = (parseInt(postData.chainBlocknumber, 16) + 0x00000a).toString(16);
                    postData.chainBlocknumber = hexChain;
                    postData.walletBlocknumber = hexWallet;
                    timeout = 2000;
                }
                setTimeout(function(){
                    if (webSocket.readyState == 1){
                        webSocket.send( JSON.stringify({
                            'action' : 'checkTransactions',
                            'postData' : postData,
                        }));
                    } else {
                        //startWebSocket();
                    }
                }, timeOut);
            }
        };
    }

    function getWsState(ws){
        var readyState = ws.readyState;
        console.log('[ws] readyState:', readyState);
        if (readyState == 3){
            startWebSocket();
        }else{
            setTimeout(function(){
                getWsState(ws);
            }, 5000);
        }
    }


    function showTransactionRow(tx){
        if ($('tr[data-key="' + tx.id_token + '"]').length){
            $('tr[data-key="' + tx.id_token + '"]').html(tx.row);
        } else {
            $('<tr data-key="' + tx.id_token + '"><td>' + tx.row + '</td></tr>').prependTo(".table-98 > tbody");
        }
        $('tr[data-key="' + tx.id_token + '"]').addClass("animationTransaction");
        console.log('[ws] push options',tx.pushoptions)
        if (Object.keys(tx.pushoptions).length === 0){
            console.log('[ws/bc] push options è vuoto');
        }else{
            displayPushNotification(tx.pushoptions);
        }
        $('#total-balance').addClass('animationBalanceIn');
        $('.star-total-balance').addClass('animationStar');
        $('#total-balance').text(tx.balance);
    }
});
