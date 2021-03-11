onmessage = e => {
    const message = e.data;

    console.log(`[From Main notifications]: ${message}`);

    if (message.action == `start`){
        console.log(`[call function]: ${message.action}`);
        var getUrl = parseUrl(`backend/notify`);
        check(getUrl);
    }

    // if (message.action == `check-transactions`){
    //     console.log(`[call function]: ${message.action}`);
    //     blockchain.checkTransactions(message.postData);
    // }


}

function parseUrl(action){
    return `../../index.php?r=` + action;
}


function check(getUrl)
{
    console.log('[notification: check] Start process');
    fetch(getUrl, {
        method: 'GET',
        dataType: "json",
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(json) {
        // console.log('[notification worker] Risposta di backend-notify',json);
        postMessage(json);
        setTimeout(function(){ check(getUrl) }, 5000);
    })
}
