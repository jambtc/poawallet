onmessage = e => {
    const message = e.data;

    if (message.action == `start`){
        // console.log(`[ethtx] action: ${message.action}`);
        // console.log(`[ethtx] user_id: ${message.user_id}`);
        // call start function

        ethtx.checkTransactions(message.user_id).then(function(json) {
            // console.log('[ethtx] Json response is: ', json);
            postMessage(json);
        })
        .catch(function(err){
            console.log('[ethtx] Error while checking data', err);
            postMessage(JSON.stringify({'success': false}));
        });
        
    }
}

function parseUrl(action){
    return `../../index.php?r=` + action;
}


var ethtx = {
    checkTransactions: async function (user_id){
        //console.log('[ethtx start sync]');
        var url = parseUrl(`blockchain/ethtx`);

        let response = await fetch(url, {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({'user_id': user_id})
        });
        return await response.json();
    },

};
