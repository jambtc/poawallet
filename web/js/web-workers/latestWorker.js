onmessage = e => {
    const message = e.data;

    // console.log(`[bc worker]: ${message}`);

    if (message.action == `latest`){
        // console.log(`[bc worker]: ${message.action}`);
        blockchain.checkLatestTransactions().then(function(json) {
            postMessage(json.transactions);
        })
        .catch(function(err){
            console.log('[bc worker] Error while checking data', err);
            postMessage(JSON.stringify({'success': false}));
        });

    }


}

function parseUrl(action){
    return `../../index.php?r=` + action;
}



var blockchain = {
    checkLatestTransactions: async function (){
        var checkTransactionsUrl = parseUrl(`blockchain/check-latest`);

        let response = await fetch(checkTransactionsUrl, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            // body: JSON.stringify({'user_id': user_id})
        });
        return await response.json();


        // let response = await fetch(checkTransactionsUrl, {
        //     method: 'POST',
        //     dataType: "json",
        //     // body: form_data,
        // })
        // .then(function(response) {
        //     return response.json();
        // })
        // .then(function(json) {
        //
        //     console.log('[bc worker] response', json);
        //
        //     var transactions = json.transactions;
        //     console.log('[bc] Transactions are: ', transactions);
        //     if (transactions){
        //         postMessage(transactions);
        //     }
        //     // ripete adesso il processo
        //     setTimeout(function(){ blockchain.checkLatestTransactions() }, 10000);
        // })
        // .catch(function(err){
        //     console.log('[bc worker] Error while checking blockchain data', err);
        //     setTimeout(function(){ blockchain.checkLatestTransactions() }, 30000);
        // })
    },

};
