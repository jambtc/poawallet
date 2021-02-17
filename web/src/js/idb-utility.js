var dbPromise = idb.open('megapay', 1, function(db) {

	//store per sincronizzazione ricezione
	// if (!db.objectStoreNames.contains('sync-receive'))
	//  	db.createObjectStore('sync-receive', {keyPath: 'id'});
	// // STORE PER STORAGE DATI DI RICEZIONE
	// if (!db.objectStoreNames.contains('np_receive'))
	// 	db.createObjectStore('np_receive', {keyPath: 'id'});

	//store per sincronizzazione check address
	// if (!db.objectStoreNames.contains('np_checkaddress'))
	// 	db.createObjectStore('np_checkaddress', {keyPath: 'id'});
	//
	// //store per sincronizzazione del gas price
	// if (!db.objectStoreNames.contains('np_gasPrice'))
	// 	db.createObjectStore('np_gasPrice', {keyPath: 'id'});



	// STORE PER SINCRONIZZAZIONE INVIO ETH E TOKEN
	// if (!db.objectStoreNames.contains('sync-send-eth'))
	//  	db.createObjectStore('sync-send-eth', {keyPath: 'id'});
	if (!db.objectStoreNames.contains('sync-send-erc20'))
	 	db.createObjectStore('sync-send-erc20', {keyPath: 'id'});
	// if (!db.objectStoreNames.contains('np-send-eth'))
	//  	db.createObjectStore('np-send-eth', {keyPath: 'id'});
	// if (!db.objectStoreNames.contains('np-send-erc20'))
	//  	db.createObjectStore('np-send-erc20', {keyPath: 'id'});

	//store per sincronizzazzione check txFound
	// if (!db.objectStoreNames.contains('sync-txPool'))
	//  	db.createObjectStore('sync-txPool', {keyPath: 'id'});
	// if (!db.objectStoreNames.contains('np-txPool'))
	//  	db.createObjectStore('np-txPool', {keyPath: 'id'});

	// store per allarme covid degli istituti
	// if (!db.objectStoreNames.contains('sync-alarm'))
	// 	db.createObjectStore('sync-alarm', {keyPath: 'id'});
	// if (!db.objectStoreNames.contains('sync-countdown'))
	// 	db.createObjectStore('sync-countdown', {keyPath: 'id'});


	//store per sincronizzazzione blockchain
	if (!db.objectStoreNames.contains('sync-blockchain'))
		db.createObjectStore('sync-blockchain', {keyPath: 'id'});

	if (!db.objectStoreNames.contains('np-transactions'))
		db.createObjectStore('np-transactions', {keyPath: 'id'});

	//store per il salvataggio della sottoscrizione push
	// if (!db.objectStoreNames.contains('subscriptions')) {
	//  	db.createObjectStore('subscriptions', {keyPath: 'id'});
	// }
	//store per verificare la presenza del wallet
	if (!db.objectStoreNames.contains('wallet')) {
	 	db.createObjectStore('wallet', {keyPath: 'id'});
	}

	console.log('ma sono arrivato wui??');

	//store per il salvataggio dei dati del pin
	if (!db.objectStoreNames.contains('pin')) {
	 	db.createObjectStore('pin', {keyPath: 'id'});
	}
	if (!db.objectStoreNames.contains('isPinRequest')) {
	 	db.createObjectStore('isPinRequest', {keyPath: 'id'});
	}
	if (!db.objectStoreNames.contains('isPinLocked')) {
	 	db.createObjectStore('isPinLocked', {keyPath: 'id'});
	}
	//store per il salvataggio del seed
	if (!db.objectStoreNames.contains('mseed')) {
	 	db.createObjectStore('mseed', {keyPath: 'id'});
	}
});


// datas with same id will be overwritten, otherwise
// wil be added
function writeData(table, data) {
	console.log('[IndexedDb storing datas]', table, data);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readwrite');
			var store = tx.objectStore(table);
			store.put(data);
			return tx.complete;
		});
}

function readAllData(table) {
	console.log("[IndexedDb read table]", table);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readonly');
			var store = tx.objectStore(table);
			return store.getAll();
		});
}

function readFromId(table,id) {
	console.log("[IndexedDb read table from id]", table, id);
	return dbPromise
		.then(function(db) {
			var tx = db.transaction(table, 'readonly');
			var store = tx.objectStore(table);
			return store.getAll(id);
		});
}

function clearAllData(table) {
	console.log("[IndexedDb delete table]", table);
  return dbPromise
    .then(function(db) {
      var tx = db.transaction(table, 'readwrite');
      var store = tx.objectStore(table);
      store.clear();
      return tx.complete;
    });
}


function deleteItemFromData(table, id){
	return dbPromise
		.then(function(db){
			var tx = db.transactions(table, 'readwrite');
			var store = tx.objectStore(table);
			store.delete(id);
			return tx.complete;
		})
		.then(function(){
			console.log('Item deleted');
		});
}

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64 = (base64String + padding)
    .replace(/\-/g, '+')
    .replace(/_/g, '/');

  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);

  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function dataURItoBlob(dataURI) {
  var byteString = atob(dataURI.split(',')[1]);
  var mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0]
  var ab = new ArrayBuffer(byteString.length);
  var ia = new Uint8Array(ab);
  for (var i = 0; i < byteString.length; i++) {
    ia[i] = byteString.charCodeAt(i);
  }
  var blob = new Blob([ab], {type: mimeString});
  return blob;
}

// Generate random entropy for the seed based on crypto.getRandomValues.
function generateEntropy(length) {
	var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	var i;
	var result = "";

	values = new Uint32Array(length);
	window.crypto.getRandomValues(values);
	for(var i = 0; i < length; i++)
	{
		result += charset[values[i] % charset.length];
	}
	return result;
}

function WordCount(str) {
  return str.split(" ").length;
}


function displayNotification(options){
	if ('serviceWorker' in navigator) {
		navigator.serviceWorker.ready
			.then(function(swreg) {
				swreg.showNotification(options.title, options);
			});

	}
}
