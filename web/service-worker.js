// import IndexedDB
importScripts('src/js/idb.js');
importScripts('src/js/idb-utility.js');

var CACHE_STATIC_NAME = 'poawallet-static-014';
var CACHE_DYNAMIC_NAME = 'poawallet-dynamic-014';

var OFFLINE_URL = 'offline.html';

var STATIC_FILES = [
	'/',
	OFFLINE_URL,
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
	'js/clipboard-copy.js',
	'js/ws-websocket.js',
	'js/web-workers/eth-tx.js',

	// search latest transactions
	'js/ws-latest.js',
	'js/web-workers/latestWorker.js',

	// manage notifications
	'js/notifications-MainWorker.js',
	'js/web-workers/notificationsWorker.js',

	'js/pincode/pincode-global.js',
	'js/pincode/pincode-settings.js',
	'js/pincode/pincode-utility.js',

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



// restituisco sempre l'originale e non carico da cache
self.addEventListener('fetch', function (event) {
	var parser = new URL(event.request.url);

	if (event.request.mode === 'navigate' && navigator.onLine === false) {
	   // Uh-oh, we navigated to a page while offline. Let's show our default page.
	   event.respondWith(caches.match(OFFLINE_URL));
	   return;

	}

	if (getFileExtension(parser.pathname) == 'php')
	{
		console.log('[SW Parser] web no-cache',parser.pathname);
		if (getFileExtension(parser.search) == '?r=wallet/index'){
			console.log('[SW Parser] web no-cache & wallet/index non fa nulla...',parser.search);
			// event.respondWith(
			// 	fetch(event.request)
			// );
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
											return cache.match(OFFLINE_URL);
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
			image: info.image,
			sound: info.sound,
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

	// console.log('[SW event on notification click] ACTION', action);
	// console.log('[SW event on notification click] URL', action.openUrl);

	//
	event.notification.close();
	if (event.action !== 'close') {
    	console.log('handle go to url with yes button');
    	event.waitUntil(clients.openWindow(action.openUrl));
  	}


}
, false);
