<?php

use yii\helpers\Url;
use yii\web\View;

$options = [
    'invalidSeedMEssage' => Yii::t('lang','Invalid seed!'),
    'baseUrl' => Yii::$app->request->baseUrl,
    'language' => Yii::$app->language,
    'cryptURL' => Url::to(['/wallet/crypt']),
    // ...
];
$this->registerJs(
    "var yiiOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    View::POS_HEAD,
    'yiiOptions'
);



$wallet_restore = <<<JS

	var seed = null;
	var lw = lightwallet;

    var wizardForm = document.querySelector('#wizard-form');
    var submitButton = document.querySelector('.seed-submit');

    submitButton.addEventListener('click', function(event){
		event.preventDefault();
		event.stopPropagation();

		seed = $('#wizardwalletform-seed').val();
		if (WordCount(seed) != 12 || !(isSeedValid(seed)) ){
			console.log('[Restore]: seed non valido', seed);
			$('#seed-error').show().text(yiiOptions.invalidSeedMEssage);
			return;
		}
		$('#seed-error').hide().text('');

		// la password viene generata in automatico dal sistema di 32 caratteri
		var password = generateEntropy(64);

		console.log('[Restore]: seed valido', seed);
		initializeVault(password,seed);
	});

	// verifica la validità di un seed
	function isSeedValid(seed){
		if (!lw.keystore.isSeedValid(seed))
			return false;
	 	else
			return true;
	}

    // adesso salviamo in local storage il seed e la password
    function initializeVault(password, seed) {
    	//prima crypto tramite php la pwd
    	$.ajax({
    		url: yiiOptions.cryptURL,
    		type: "POST",
    		data: {
    			'pass': password,
    			'seed': seed
    		},
    		dataType: "json",
    		success:function(data){
    			var pwd_crypted  = data.cryptedpass;
    			var seed_crypted  = data.cryptedseed;
    			var iduser_crypted  = data.cryptediduser;
    			//console.log('vault',password,seed);
    			lw.keystore.createVault({
                    password: password,
    			    seedPhrase: seed,
    			    hdPathString: "m/0'/0'/0'"
    			},  function (err, ks) {
                        ks.keyFromPassword(password,
                            function (err, pwDerivedKey) {
                		        if (!ks.isDerivedKeyCorrect(pwDerivedKey)) {
                                    throw new Error("Incorrect derived key!");
                		        }

                		        try {
                    		          ks.generateNewAddress(pwDerivedKey, 1);
                		        } catch (err) {
                    		          console.log(err);
                    		          console.trace();
                		        }
                		        var address = ks.getAddresses()[0];
                		        var prv_key = ks.exportPrivateKey(address, pwDerivedKey);

        						var walletPost = {
        							id			: address, // id of indexedDB
        							id_user		: iduser_crypted,
        							prv_php 	: CryptoJS.AES.encrypt(JSON.stringify(prv_key), password, {format: CryptoJSAesJson}).toString(),
        							prv_pas		: pwd_crypted,
        						};
        						console.log('[Restore]: address and key in post: ', walletPost);

                                clearAllData('wallet')
                                .then(function () {
                                    writeData('wallet', walletPost)
                                    .then(function() {
                                        console.log('[Restore]: Saved wallet info in indexedDB', walletPost);
                                        //$('#cryptConferma').html('<img width=20 src="'+ajax_loader_url+'" alt="'+Yii.t('js','loading...')+'">');
                                    })
                                    .then(function() {
                                        var seedPost = {
                                            id : new Date().toISOString(), // id of indexedDB
                                            cryptedseed : seed_crypted,
                                        }
                                        writeData('mseed', seedPost)
                                        .then(function() {
                                            // imposta il valore dell'address nella campo input nascosto
                                            $('#wizardwalletform-address').val(address);
                                            // Quindi, chiedo di installare la webapp sulla home del cell
                                            saveOnDesktop();
                                            // quindi invio il submit per salvare
                                            // l'address in archivio
                                            setTimeout(function(){
                                                wizardForm.submit ();
                                            }, 50);
                                        });
                                    });

                                });
                            }
                        )
    				});
    		},
    		error: function(j){
    			console.log('ajax error',j);
    		}
    	});
    }

    // chiede di salvare l'applicazione sulla home
    function saveOnDesktop() {
    	if (deferredPrompt) {
    		deferredPrompt.prompt();
    		deferredPrompt.userChoice.then(function(choiceResult) {
    			 console.log('[deferred prompt]',choiceResult.outcome);
    			if (choiceResult.outcome === 'dismissed') {
    	  			console.log('[deferred prompt] User cancelled installation');
    			} else {
    	  			console.log('[deferred prompt] User added to home screen');
    			}
    		});
    		deferredPrompt = null;
    	}
    }

JS;

$this->registerJs(
    $wallet_restore,
    View::POS_READY, //POS_END
    'wallet_restore'
);
