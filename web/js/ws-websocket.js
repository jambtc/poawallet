$(function () {

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
            webSocket.send( JSON.stringify({
                'action' : 'notifications',
            }));
            getWsState(webSocket);

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

            switch (response.command){
                case 'check-transactions':
                    if (response.success == true){
                        $('.pulse-button').removeClass('pulse-button-offline');

                        // analizzare la risposta delle transazioni
                        var transactions = response.transactions;
                        console.log('[ws] Transactions are: ', transactions);
                        if (transactions){
                            for (var tx of transactions) {
                                console.log('[ws] single transaction data:', tx);
                                showTransactionRow(tx);
                            }
                        }
                        var postData = {
                            search_address: response.user_address, // indirizzo da controllare
                            chainBlocknumber: response.chainBlocknumber,
                            walletBlocknumber: response.walletBlocknumber,
                        };
                        var timeOut = 0;

                        if (response.difference > 0){
                            $('.header-message').html(response.headerMessage);
                        } else {
                            $('.header-message').html('');
                            postData.chainBlocknumber += 10;
                            timeout = 10000;
                        }
                        setTimeout(function(){
                            webSocket.send( JSON.stringify({
                                'action' : 'checkTransactions',
                                'postData' : postData,
                            }));
                        }, timeOut);
                    }

                    break;

                case 'notifications':
                    notify.handleResponse(response);

                    setTimeout(function(){
                        webSocket.send( JSON.stringify({
                            'action' : 'notifications',
                        }));
                    }, 5000);
                    break;
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
            }, 10000);
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

    notify = {
        handleResponse: function(response)
        {
            console.log('[ws] handle notification messages');
            if (response.playAlarm == true){
                notify.Alarm();
            }
            if (response.playSound == true){
                notify.playSound();
            }
            $('#quantity_notify').html(response.countedUnread);
            $('.notification-list').html(response.htmlTitle);
            $('.notification-list').append(response.htmlContent);
            if (response.countedUnread > 0){
                $("#quantity_circle").fadeIn(1000).show();
                $('.notify-readAll').show();
            } else {
                $("#quantity_circle").fadeIn(1000).hide();
                $('.notify-readAll').hide();
            }
        },
        openAllEnvelopes: async function(){
            try {
                let response = await $.ajax({
                    url:'index.php?r=backend/update-all-news',
                    type: "POST",
                    data: {},
                    dataType: 'json',
                    success: function(response) {
                        console.log('[ws] All notifications updated.',response);
                    },
                    error: function(data) {
                        console.log(data);
                    },
                });
            } catch (e) {
                console.log("[ws: open all envelopes] ERRORE in async function. Si è verificato un errore!");
            }
        },
        openEnvelope: async function(id_notification){
            event.preventDefault();
            event.stopPropagation();
            var submitUrl = $('#news_'+id_notification).attr('href');

            // metto a read il valore del messaggio
            try {
                let response = await $.ajax({
                    url:'index.php?r=backend/update-single-news',
                    type: "POST",
                    data: { 'id_notification' : id_notification },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success)
                            location.href = submitUrl;
                        },
                    error: function(data) {
                        console.log(data);
                    },
                });
            } catch (e) {
                console.log("[ws: open single envelope] ERRORE in async function. Si è verificato un errore!");
            }
        },

    };
});
