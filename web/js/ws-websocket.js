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
            // webSocket.send( JSON.stringify({
            //     'action' : 'notifications',
            // }));
            // webSocket.send( JSON.stringify({
            //     'action' : 'checkLatest',
            // }));
            getWsState(webSocket);

        };

        // in caso di errore di connessione o server down riavvio
        webSocket.onerror = function(event) {
            console.error("[ws] WebSocket error observed:", event);
            $('.pulse-button').addClass('pulse-button-offline');
            // trying to restart
            setTimeout(function(){ startWebSocket() }, 10000);
        };

        // webSocket.onclose = function(e) {
        //     console.log('[ws] Disconnected!');
        //     // $('.pulse-button').addClass('pulse-button-offline');
        //     startWebSocket();
        // };

        // gestico i messaggi di risposta dal server
        webSocket.onmessage = function(e) {
            var response = JSON.parse(e.data);

            // switch (response.command){
            //     case 'check-transactions':
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
                            var hexValue = (parseInt(postData.chainBlocknumber, 16) + 0x000005).toString(16);
                            postData.chainBlocknumber = hexValue;
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
                    // break;

                // case 'notifications':
                //     notify.handleResponse(response);
                //
                //     setTimeout(function(){
                //         if (webSocket.readyState == 1){
                //             webSocket.send( JSON.stringify({
                //                 'action' : 'notifications',
                //             }));
                //         } else {
                //             //startWebSocket();
                //         }
                //     }, 2000);
                //     break;
                //
                // case 'check-latest':
                //     if (response.success == true){
                //         $('.pulse-button').removeClass('pulse-button-offline');
                //         // analizzare la risposta delle transazioni
                //         var transactions = response.transactions;
                //         console.log('[ws] Latest Transactions are: ', transactions);
                //         if (transactions){
                //             for (var tx of transactions) {
                //                 console.log('[ws] latest transaction data:', tx);
                //                 showTransactionRow(tx);
                //             }
                //         }
                //         console.log('[ws] Latest difference:', response.difference);
                //         // if (response.difference > 1){
                //             setTimeout(function(){
                //                 if (webSocket.readyState == 1){
                //                     webSocket.send( JSON.stringify({
                //                         'action' : 'checkLatest',
                //                     }));
                //                 } else {
                //                     //startWebSocket();
                //                 }
                //             }, 5000);
                //         // }
                //     }
                //     break;
            // }

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

    // notify = {
    //     handleResponse: function(response)
    //     {
    //         console.log('[ws] handle notification messages');
    //         if (response.playAlarm == true){
    //             notify.Alarm();
    //         }
    //         if (response.playSound == true){
    //             notify.playSound();
    //         }
    //         $('#quantity_notify').html(response.countedUnread);
    //         $('.notification-list').html(response.htmlTitle);
    //         $('.notification-list').append(response.htmlContent);
    //         if (response.countedUnread > 0){
    //             $("#quantity_circle").fadeIn(1000).show();
    //             $('.notify-readAll').show();
    //         } else {
    //             $("#quantity_circle").fadeIn(1000).hide();
    //             $('.notify-readAll').hide();
    //         }
    //     },
    //     openAllEnvelopes: async function(){
    //         try {
    //             let response = await $.ajax({
    //                 url:'index.php?r=backend/update-all-news',
    //                 type: "POST",
    //                 data: {},
    //                 dataType: 'json',
    //                 success: function(response) {
    //                     console.log('[ws] All notifications updated.',response);
    //                 },
    //                 error: function(data) {
    //                     console.log(data);
    //                 },
    //             });
    //         } catch (e) {
    //             console.log("[ws: open all envelopes] ERRORE in async function. Si è verificato un errore!");
    //         }
    //     },
    //     openEnvelope: async function(id_notification){
    //         event.preventDefault();
    //         event.stopPropagation();
    //         var submitUrl = $('#news_'+id_notification).attr('href');
    //
    //         // metto a read il valore del messaggio
    //         try {
    //             let response = await $.ajax({
    //                 url:'index.php?r=backend/update-single-news',
    //                 type: "POST",
    //                 data: { 'id_notification' : id_notification },
    //                 dataType: 'json',
    //                 success: function(response) {
    //                     if (response.success)
    //                         location.href = submitUrl;
    //                     },
    //                 error: function(data) {
    //                     console.log(data);
    //                 },
    //             });
    //         } catch (e) {
    //             console.log("[ws: open single envelope] ERRORE in async function. Si è verificato un errore!");
    //         }
    //     },
    //
    // };
});
