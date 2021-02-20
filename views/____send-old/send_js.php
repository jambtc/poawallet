<?php

use yii\helpers\Url;
use yii\web\View;

use app\components\Settings;

// Yii::$classMap['settings'] = Yii::getAlias('@packages').'/settings.php';

$options = [
    'baseUrl' => Yii::$app->request->baseUrl,
    'language' => Yii::$app->language,
    'sendURL' => Url::to(['/send/generate-transaction']),
    'poaDecimals' => Settings::load()->poa_decimals,
    'invalidAmountError' => Yii::t('lang', 'Invalid amount!'),
    'decimalError' => Yii::t('lang','Use a maximum of 2 decimal places.'),
    'higherError' => Yii::t('lang','Amount is higher than Balance.'),
    'recipientError' => Yii::t('lang','Recipient address not entered.'),
    'htmlTransactionBody' => '<div class="alert alert-warning">
                                <p class="generating">'.Yii::t('lang','Generating transaction...').'</p>
                                </div>',
    //'textClose' => Yii::t('lang','Close'),
    // ...
];
$this->registerJs(
    "var yiiOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    View::POS_HEAD,
    'yiiOptions'
);



$wallet_restore = <<<JS

    var countDecimals = function(value) {
        // console.log('[countDecimals]',Math.floor(value),value);
        if (Math.floor(value) != value)
            return value.toString().split(".")[1].length || 0;
        return 0;
    }

    var sendForm = document.querySelector('#send-form');
    var stepButton = document.querySelector('#stepwizard_step1_save');
    var submitButton = document.querySelector('.pay-submit');

    stepButton.addEventListener('click', function(event){
        $('.amount-to-send').text($('#sendtokenform-amount').val());
        $('#error-summary').hide().text('');

        if ($("#sendtokenform-amount").val() <= 0 ){
            $('#error-summary').show().text(yiiOptions.invalidAmountError);
            event.stopPropagation();
		}

        if (countDecimals($("#sendtokenform-amount").val()) > yiiOptions.poaDecimals){
			$('#error-summary').show().text(yiiOptions.decimalError);
			event.stopPropagation();
		}

		if (eval($("#sendtokenform-amount").val()) > eval($("#sendtokenform-balance").val())){
            $('#error-summary').show().text(yiiOptions.higherError);
            event.stopPropagation();
		}

		if ($("#sendtokenform-to").val() == ''){
            $('#error-summary').show().text(yiiOptions.recipientError);
            event.stopPropagation();
		}
	});


    submitButton.addEventListener('click', function(event){
        event.preventDefault();
		event.stopPropagation();
        console.log('[Send]: button pressed');

        my_wallet = $('#sendtokenform-from').val();

		var sendPost = {
			id		: new Date().toISOString(), // id of indexedDB
			from	: my_wallet,
			to		: $('#sendtokenform-to').val(),
			amount	: $('#sendtokenform-amount').val(),
			memo 	: $('#sendtokenform-memo').val(),
			prv_key : null,
			prv_pas : null,
		};
		console.log('[Send]: sendPost senza chiave',sendPost);

		// USO IL SERVICE WORKER
		if ('serviceWorker' in navigator && 'SyncManager' in window){
			navigator.serviceWorker.ready
			.then(function(sw) {
				var serWork = sw; // firefox fix
				//leggo la priv_key dallo storage
				var prv_key = null;
				readAllData('wallet').then(function(data) {
                    if (typeof data[0] !== 'undefined') {
						sendPost.prv_key = data[0].prv_php;
						sendPost.prv_pas = data[0].prv_pas;
						console.log('[Send]: sendPost con chiave',sendPost);

                        $.ajax({
            				url	: yiiOptions.sendURL, // ERC20 send url,
            				type: "POST",
            				data: sendPost,
            				dataType: "json",
                            beforeSend: function() {
        						$('.card-body').hide();
                                $('.wizard-inner').html('');
                                $('.pay-submit').hide();
        						$('.card-body').after(yiiOptions.htmlTransactionBody);
        					},
            				success:function(data){
            					console.log('[send]: data from generate-transaction controller',data);
                                $('.generating').parent().removeClass('alert-warning');
                                $('.generating').html(data.row);
                                $('.pay-close').show();

                                writeData('sync-send-erc20', data).then(function() {
        							console.log('[Send]: Registered sync-send-erc20 request in indexedDB', data);
        							return serWork.sync.register('sync-send-erc20');
        						})
                                .then(function() {
        							erc20.isReadySent(data.id);
        						})

            				},
            				error: function(j){
            					console.log(j);
            				}
            			});
					} else {
						console.log('Chiave privata non trovata!');
						return;
					}
				})
			});
		}
	});

    var erc20 = {
        isReadySent: function(id){
            readFromId('np-send-erc20',id)
            .then(function(data) {
                console.log('[isReadySent]: checking data from np-send-erc20 salvato dal SW', data);
                if (typeof data[0] !== 'undefined' && data[0].id == id && data[0].status != 'new' )
                {
                    console.log('id token è:', id);
                    $('.generating').addClass('animationTransaction');
                    $('.generating').html(data[0].row);
                    $('#total-balance').addClass('animationBalanceIn');
                    $('.star-total-balance').addClass('animationStar');
                    $('#total-balance').text(data[0].balance);

                    clearAllData('np-send-erc20');
                } else {
                    setTimeout(function(){ erc20.isReadySent(id) }, 1000);
                }
            });
        },
    }

JS;

$this->registerJs(
    $wallet_restore,
    View::POS_READY, //POS_END
    'wallet_restore'
);
