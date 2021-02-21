onmessage = e => {
    const message = e.data;

    console.log(`[From Main sync-blockchain]: ${message}`);

    if (message.action == `sync`){
        console.log(`[call function]: ${message.action}`);
        blockchain.getBlockNumber();
    }

    if (message.action == `check-transactions`){
        console.log(`[call function]: ${message.action}`);
        blockchain.checkTransactions(message.postData);
    }


}

function parseUrl(action){
    return `../../index.php?r=` + action;
}



var blockchain = {
    getBlockNumber: function(){
        // console.log('[blockchain: sync] Start async process');
        var getBlocknumberUrl = parseUrl(`blockchain/get-blocknumber`);

        // console.log('[blockchain: sync] getBlocknumberUrl', getBlocknumberUrl);

        fetch(getBlocknumberUrl, {
            method: 'GET',
            dataType: "json",
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(json) {
            // console.log('[Service worker] Risposta di get-blocknumber',json);
            postMessage(json);
        })
    },


    checkTransactions: function (postData){
        // console.log('[blockchain: sync] postData', postData);
        var checkTransactionsUrl = parseUrl(`blockchain/check-transactions`);

        // console.log('[blockchain: sync] checkTransactionsUrl', checkTransactionsUrl);

        var form_data = new FormData();
        for ( var key in postData ) {
            form_data.append(key, postData[key]);
        }
        fetch(checkTransactionsUrl, {
            method: 'POST',
            dataType: "json",
            body: form_data,
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(json) {
            if (json.success){
                // console.log('[Service worker] Risposta da url: '+checkTransactionsUrl,json);
                const title = json.transactions[0].title;
                const options = {
                    body: json.transactions[0].message,
                    icon: 'src/images/icons/app-icon-96x96.png',
                    vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
                    badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
                    tag: 'confirm-notification', //tag univoco per le notifiche.
                    renotify: true, //connseeo a tag. se è true notifica di nuovo
                    data: {
                       openUrl: json.transactions[0].url,
                    },
                    actions: [
                        {action: 'openUrl', title: 'Yes', icon: 'css/images/chk_on.png'},
                        {action: 'close', title: 'No', icon: 'css/images/chk_off.png'},
                    ],
                };
                displayPushNotification(title,options)
                // self.registration.showNotification(title, options);
                //writeData('sync-blockchain', json);
            }

            //

            // ripete adesso il processo
            // blockchain.getBlockNumber();
            setTimeout(function(){ blockchain.getBlockNumber() }, 1500);
        })
        .catch(function(err){
            console.log('[Service worker] Error while checking blockchain data', err);
            setTimeout(function(){ blockchain.getBlockNumber() }, 60500);
        })
    },

};
