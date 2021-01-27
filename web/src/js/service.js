var deferredPrompt;

//Polyfill: per i browser più vecchi che non hanno window.Promise
if (!window.Promise){
	window.Promise = Promise;
}

if ('serviceWorker' in navigator){
	navigator.serviceWorker
		//.register('sw.js')
		.register('sw.js').then(reg => {
		  reg.installing; // the installing worker, or undefined
		  reg.waiting; // the waiting worker, or undefined
		  reg.active; // the active worker, or undefined

		  reg.addEventListener('updatefound', () => {
		    // A wild service worker has appeared in reg.installing!
		    const newWorker = reg.installing;

		    newWorker.state;
		    // "installing" - the install event has fired, but not yet complete
		    // "installed"  - install complete
		    // "activating" - the activate event has fired, but not yet complete
		    // "activated"  - fully active
		    // "redundant"  - discarded. Either failed install, or it's been
		    //                replaced by a newer version

		    newWorker.addEventListener('statechange', () => {
		    	// newWorker.state has changed
			  	console.log('[Service worker] ... new state',newWorker.state);
		    });
		  });
		})
		.then(function (){
			console.log('[Service worker] ... from service registered.');
		})
		.catch(function(err) {
	   		console.log("Service Worker Failed to Register", err);
   		});
}



window.addEventListener('beforeinstallprompt', function(event){
	console.log('[service] beforeinstallprompt fired!');
	event.preventDefault();
	deferredPrompt = event;
	return false;
});
