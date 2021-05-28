let wssstop = 0;
let webSocket;
console.log('wss-stop',wssstop);

$(function () {
    var countError = 0;
    let wssalert = document.getElementById('wss_server');

    startWebSocket();

    function startWebSocket(){
        webSocket = new WebSocket(yiiGlobalOptions.WebSocketServerAddress);

        // console.log('[ws] readyState:', readyState);
        // all'apertura leggi il numero di blocchi
        webSocket.onopen = function(e) {
            hideWssAlert();
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
            console.log('[ws] Json response is: ', response);

            if (response.success == true){
                $('.pulse-button').removeClass('pulse-button-offline');

                networkDetails(response);

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
        console.log('[ws] readyStop:', wssstop);
        if (readyState == 3 && wssstop == 0){
            startWebSocket();
        }else{
            setTimeout(function(){
                getWsState(ws);
            }, 5000);
        }
    }

    function showWssAlert() {
        wssalert.className = 'show';
    }

    function hideWssAlert() {
        wssalert.className = 'hide';
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



});
