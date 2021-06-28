onmessage = e => {
    const message = e.data;

    console.log(`[From ethtx]: ${message}`);

    if (message.action == `start`){
        console.log(`[ethtx] call function: ${message.action}`);
        var getUrl = parseUrl(`blockchain/ethtx`);
        check(getUrl);
    }

}

function parseUrl(action){
    return `../../index.php?r=` + action;
}


function check(getUrl)
{
    console.log('[ethtx] Start process');
    fetch(getUrl, {
        method: 'POST',
        dataType: "json",
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(json) {
        // console.log('[notification worker] Risposta di backend-notify',json);
        postMessage(json);
        setTimeout(function(){ check(getUrl) }, 10000);
    })
}
