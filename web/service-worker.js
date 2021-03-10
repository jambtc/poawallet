// import IndexedDB
importScripts('src/js/idb.js');
importScripts('src/js/idb-utility.js');

var CACHE_STATIC_NAME = 'megapay-static-006';
var CACHE_DYNAMIC_NAME = 'megapay-dynamic-007';

var STATIC_FILES = [
	'/',
	'offline.html',
	'manifest.json',

	'favicon.ico',
	'css/global.style.css',
	'css/img/content/coin5.png',
	'css/img/content/dash-bg.png',
	'css/img/content/icons/2.png',
	'css/img/content/icons/5.png',
	'css/pincode.css',
	'css/site.css',
	'css/yiipager.css',

	'js/global.script.js',
	'js/notifications-MainWorker.js',
	'js/pincode/pincode-global.js',
	'js/pincode/pincode-settings.js',
	'js/pincode/pincode-utility.js',

	'js/ws-blockchain.js',
	'js/ws-latest.js',
	'js/nfc-write.js',

	'js/web-workers/bcWorker.js',
	'js/web-workers/notificationsWorker.js',

	'src/images/icons/app-icon-144x144.png',

	'src/js/promise.js',
	'src/js/fetch.js',
	'src/js/idb.js',
	'src/js/idb-utility.js',
	'src/js/service.js',
];



// Funzione Fix per apache
function cleanResponse(response) {
	const clonedResponse = response.clone();

	// Not all browsers support the Response.body stream, so fall back to reading
	// the entire body into memory as a blob.
	const bodyPromise = 'body' in clonedResponse ?
	  Promise.resolve(clonedResponse.body) :
	  clonedResponse.blob();

	return bodyPromise.then((body) => {
	  // new Response() is happy when passed either a stream or a Blob.
	  return new Response(body, {
			headers: clonedResponse.headers,
			status: clonedResponse.status,
			statusText: clonedResponse.statusText,
	  });
	});
}

function trimCache(cacheName, maxItems) {
	console.log('[Service Worker] trimming caches...');

	caches.open(cacheName)
		.then(function(cache) {
			return cache.keys()
				.then(function(keys) {
					if (keys.lenght > maxItems) {
						cache.delete(keys[0])
							.then(trimCache(cacheName, maxItems));
					}
				});
		});
}

self.addEventListener('message', function (event) {
	if (event.data.action === 'skipWaiting') {
    	self.skipWaiting();
		trimCache(CACHE_STATIC_NAME,0);
		trimCache(CACHE_DYNAMIC_NAME,0);
	}
});


self.addEventListener('install', function (event) {
	//console.log('[Service Worker] Installing Service worker...', event);
	console.log('[Service Worker] Installing Service worker...');
	event.waitUntil(
		//versioning della cache. Per aggiornare le versioni del software
		caches.open(CACHE_STATIC_NAME)
			.then(function(cache){
				console.log('[Service Worker] Precaching app shell...');
				cache.addAll(STATIC_FILES);
			})
	)
});
self.addEventListener('activate', function (event) {
	console.log('[Service Worker] Activating Service worker...');
	event.waitUntil(
		caches.keys()
			.then(function(keyList) {
				return Promise.all(keyList.map(function(key){
					if (key !== CACHE_STATIC_NAME && key !== CACHE_DYNAMIC_NAME){
						console.log('[Service Worker] deleting cache', key);
						return caches.delete(key);
					}
				}));
			})

	);
	return self.clients.claim();
});

function isInArray(string, array) {
	for(var i = 0; i < array.length; i++) {
		if (array[i] === string){
			return true;
		}
	}
	return false;
}

function getFileExtension(filename) {
  return filename.split('.').pop();
}

// this.addEventListener('fetch', event => {
//   // request.mode = navigate isn't supported in all browsers
//   // so include a check for Accept: text/html header.
//   if (event.request.mode === 'navigate' || (event.request.method === 'GET' && event.request.headers.get('accept').includes('text/html'))) {
//         event.respondWith(
//           fetch(event.request.url).catch(error => {
//               // Return the offline page
//               return caches.match('offline.html');
//           })
//     );
//   }
//   else{
//         // Respond with everything else if we can
//         event.respondWith(caches.match(event.request)
//                         .then(function (response) {
//                         return response || fetch(event.request);
//                     })
//             );
//       }
// });


// // restituisco sempre l'originale e non carico da cache
// self.addEventListener('fetch', function (event) {
// 	var parser = new URL(event.request.url);
//
//
// 	if (getFileExtension(parser.search) == '?r=send%2Findex'
// 		//|| getFileExtension(parser.pathname) == 'css'
// 	){
// 		console.log('[SW Parser] web ',parser.pathname);
// 		event.respondWith(
// 		 	fetch(event.request)
// 		);
// 	} else if (isInArray(event.request.url, STATIC_FILES)) {
// 		console.log('[SW Parser] static cache ',parser.pathname);
// 		event.respondWith(
// 			fetch(event.request).catch(function(){
// 				return	caches.match(event.request);
// 			})
//
// 		);
// 	} else {
// 		console.log('[SW Parser] dynamic cache ',parser.pathname);
// 		event.respondWith(
// 			caches.match(event.request)
// 				.then(function(response) {
// 					if (response) {
// 						// Inizio Fix per apache
// 						if(response.redirected) {
// 							return cleanResponse(response);
// 						} else {
// 							return response;
// 						}
// 						// END Fix per apache
// 					} else {
// 						return fetch(event.request)
// 							.then(function(res) {
// 								return caches.open(CACHE_DYNAMIC_NAME)
// 									.then(function(cache) {
// 										//trimCache(CACHE_DYNAMIC_NAME, 20);
// 										cache.put(event.request.url, res.clone());
// 										return res;
// 									})
// 							}).
// 							catch(function(err) {
// 								return caches.open(CACHE_STATIC_NAME)
// 									.then(function(cache) {
// 										if (event.request.mode === 'navigate' || (event.request.method === 'GET' && event.request.headers.get('accept').includes('text/html'))) {
// 										// if (event.request.headers.get('accept').includes('text/html')){
// 											return cache.match('offline.html');
// 										}else{
// 									        // Respond with everything else if we can
// 									        event.respondWith(caches.match(event.request)
// 									            .then(function (response) {
// 									                return response || fetch(event.request);
// 									            })
// 									        );
// 									     }
// 									})
// 							});
// 					}
// 				})
// 		);
// 	}
//
//
// });

// restituisco sempre l'originale e non carico da cache
self.addEventListener('fetch', function (event) {
	var parser = new URL(event.request.url);


	if (getFileExtension(parser.pathname) == 'php'
		// || getFileExtension(parser.pathname) == 'css'
	){
		console.log('[SW Parser] web ',parser.pathname);
		if (getFileExtension(parser.search) == '?r=wallet/index'){
			console.log('[SW Parser] SONO QUI. STO CARICANDO IL FILE ??? ',parser.search);
		} else {

			event.respondWith(
				fetch(event.request)
			);
		}
	} else if (isInArray(event.request.url, STATIC_FILES)) {
		console.log('[SW Parser] static cache ',parser.pathname);
		event.respondWith(
			fetch(event.request).catch(function(){
				return	caches.match(event.request);
			})

		);
	} else {
		console.log('[SW Parser] dynamic cache ',parser.pathname);
		event.respondWith(
			caches.match(event.request)
				.then(function(response) {
					if (response) {
						// Inizio Fix per apache
						if(response.redirected) {
							return cleanResponse(response);
						} else {
							return response;
						}
						// END Fix per apache
					} else {
						return fetch(event.request)
							.then(function(res) {
								return caches.open(CACHE_DYNAMIC_NAME)
									.then(function(cache) {
										//trimCache(CACHE_DYNAMIC_NAME, 20);
										cache.put(event.request.url, res.clone());
										return res;
									})
							}).
							catch(function(err) {
								return caches.open(CACHE_STATIC_NAME)
									.then(function(cache) {
										if (event.request.headers.get('accept').includes('text/html')){
											return cache.match('offline.html');
										}
									})
							});
					}
				})
		);
	}


});


//listener per la sincronizzazione in background
self.addEventListener('sync', function(event) {
	console.log('[Service Worker] Background syncing: '+event.tag, event);


	// SINCRONIZZAZIONE INVIO ERC20
	if (event.tag === 'sync-send-erc20') {
		console.log('[Service worker] Evento sincronizzazione invio token trovato!');
 		event.waitUntil(
 			readAllData(event.tag)
 			.then(function(data) {
 				for (var dt of data) {
					console.log('[Service worker] fetching sync-send-erc20',dt);
					var postData = new FormData();
	  					postData.append('id', dt.id);
						//postData.append('chainBlock', dt.chainBlock);

	 				fetch(dt.url, {
	 					method: 'POST',
	 					body: postData,
	 				})
	 				.then(function(response) {
	 					return response.json();
	 				})
	 				.then(function(json) {
						console.log('[Service worker] Risposta di send/validateTransaction',json);
						writeData('np-send-erc20', json);

				 	})
 					.catch(function(err){
 						console.log('[Service worker] Error while checking send-erc20 data', err);
 					})
 				}
 				//per sicurezza cancello tutto da indexedDB
 				clearAllData(event.tag);
 			 })
 		 );
 	}

	// SINCRONIZZAZIONE BLOCKCHAIN
	// if (event.tag === 'sync-blockchain') {
	// 	console.log('[Service worker] Evento sincronizzazione della blockchain trovato!');
 	// 	event.waitUntil(
 	// 		readAllData(event.tag)
 	// 		.then(function(data) {
 	// 			for (var dt of data) {
	// 				console.log('[Service worker] fetching sync-blockchain',dt);
	// 				var postData = new FormData();
	// 					postData.append('chainBlocknumber', dt.chainBlocknumber);
	// 					postData.append('walletBlocknumber', dt.walletBlocknumber);
	// 					postData.append('search_address', dt.search_address);
	//  				fetch(dt.url, {
	//  					method: 'POST',
	//  					body: postData,
	//  				})
	//  				.then(function(response) {
	//  					return response.json();
	//  				})
	//  				.then(function(json) {
	// 					if (json.success){
	// 						console.log('[Service worker] Risposta da url: '+dt.url,json);
	// 						const title = json.transactions[0].title;
	// 						const options = {
	// 							body: json.transactions[0].message,
	// 							icon: 'src/images/icons/app-icon-96x96.png',
	// 							vibrate: [100, 50, 100, 50, 100 ], //in milliseconds vibra, pausa, vibra, ecc.ecc.
	// 							badge: 'src/images/icons/app-icon-96x96.png', //solo per android è l'icona della notifica
	// 							tag: 'confirm-notification', //tag univoco per le notifiche.
	// 							renotify: true, //connseeo a tag. se è true notifica di nuovo
	// 							data: {
	// 							   openUrl: json.transactions[0].url,
	// 							},
	// 							actions: [
	// 								{action: 'openUrl', title: 'Yes', icon: 'css/images/chk_on.png'},
	// 								{action: 'close', title: 'No', icon: 'css/images/chk_off.png'},
	// 							],
	// 						};
	// 					  	self.registration.showNotification(title, options);
	// 						writeData('sync-blockchain', json);
	// 					}
 	// 			 	})
 	// 				.catch(function(err){
 	// 					console.log('[Service worker] Error while checking blockchain data', err);
 	// 				})
 	// 			}
 	// 			//per sicurezza cancello tutto da indexedDB
 	// 			clearAllData(event.tag);
 	// 		 })
 	// 	 );
 	// }

});


//listener per le notifiche push
self.addEventListener('push', function(event) {
  console.log('[Service Worker] Push Received.');
  //console.log(`[Service Worker] Push had this data: "${event.data.text()}"`);

	const info = JSON.parse(`${event.data.text()}`);
	console.log(`[Service Worker] Push had this data: `+info);

	const sendNotification = body => {
    return self.registration.showNotification(info.title, {
			body: info.body,
			icon: info.icon,
			badge: info.badge,
			vibrate: info.vibrate,
			//image: info.image,
			//sound: info.sound,
			data: info.data,
			actions: info.actions,
			tag: info.tag,
			renotify: true,
			data: {
				 openUrl: info.openUrl,
			},
    });
	};

  if (event.data) {
    const message = event.data.text();
    event.waitUntil(sendNotification(message));
	}
});

self.addEventListener('notificationclick', function(event) {
	console.log('[SW event on notification click] EVENT', event);

	if (typeof event.notification.data !== 'undefined') {
		var action = event.notification.data;
	}else{
		var action = JSON.parse(event.actions);
	}

	console.log('[SW event on notification click] ACTION', action);
	console.log('[SW event on notification click] URL', action.openUrl);

	event.notification.close();
	event.waitUntil(clients.openWindow(action.openUrl));

}
, false);
