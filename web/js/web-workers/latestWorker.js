onmessage = e => {
    const message = e.data;

    console.log(`[bc worker]: ${message}`);

    if (message.action == `latest`){
        console.log(`[bc worker]: ${message.action}`);
        blockchain.checkLatestTransactions();
    }


}

function parseUrl(action){
    return `../../index.php?r=` + action;
}



var blockchain = {

    checkLatestTransactions: function (){
        // console.log('[blockchain: sync] postData', postData);
        var checkTransactionsUrl = parseUrl(`blockchain/check-latest`);

        fetch(checkTransactionsUrl, {
            method: 'POST',
            dataType: "json",
            // body: form_data,
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(json) {

            // console.log('[bc worker] response', json);

            var transactions = json.transactions;
            console.log('[bc] Transactions are: ', transactions);
            if (transactions){
                postMessage(transactions);
                // // avvio la sincronizzazione
                // bcWorker.postMessage({
                //     action : "latest",
                // });
                //
                // for (var tx of transactions) {
                //     console.log('[bc] single transaction data:', tx);
                //     showTransactionRow(tx);
                // }
            }


            // ripete adesso il processo
            // blockchain.getBlockNumber();
            setTimeout(function(){ blockchain.checkLatestTransactions() }, 2000);
        })
        .catch(function(err){
            console.log('[bc worker] Error while checking blockchain data', err);
            setTimeout(function(){ blockchain.checkLatestTransactions() }, 10000);
        })
    },

};
