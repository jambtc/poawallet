<?php


$managePush = <<<JS

$(function () {
    'use strict';


    // This code checks if service workers and push messaging is supported by the current browser and if it is, it registers our sw.js file.
    // const applicationServerPublicKey = ;
    const applicationServerPublicKey = '{$vapidPublicKey}';

    // this button open the modal window
    var pushButtonModal = document.querySelector('.js-push-btn-modal');

    var pushButtonModalBack = document.querySelector('.js-push-btn-modal-back');
    var pushButton = document.querySelector('.js-push-btn');
    var pushButtonRemove = document.querySelector('.js-push-btn-remove');

    const pushButtonModalText = document.querySelector('.js-push-btn-modal-text');

    let isSubscribed = false;
    let swRegistration = null;

    if ('serviceWorker' in navigator && 'PushManager' in window) {
        console.log('[push] Push is supported');
        navigator.serviceWorker.register('service-worker.js')
            .then(function(swReg) {
                console.log('[push] Service Worker is registered again');

                swRegistration = swReg;
                initializeUI();
            })
            .catch(function(error) {
                console.error('[push] Service Worker Error', error);
            });
    } else {
        console.warn('[push] Push messaging is not supported');
        pushButtonModalText.textContent = Yii.t('js','Push Not Supported');
    }


    // check if the user is currently subscribed
    function initializeUI() {
        console.log('[push] initializeUI called.');
        pushButtonModalBack.addEventListener('click', function() {
            if ($('.js-push-btn-modal').hasClass('checked')){
                $('.js-push-btn-modal').removeClass('checked')
            } else {
                $('.js-push-btn-modal').addClass('checked')
            }
        });


        pushButton.addEventListener('click', function() {
            console.log('[push] User is NOT subscribed.');
            subscribeUser();
        });

        pushButtonRemove.addEventListener('click', function() {
            console.log('[push] User IS subscribed.');
            unsubscribeUser();
        });


        // Set the initial subscription value
        swRegistration.pushManager.getSubscription()
            .then(function(subscription) {
                isSubscribed = !(subscription === null);
                console.log('[push] isSubscribed: ',isSubscribed);

                //updateSubscriptionOnServer(subscription);

            if (isSubscribed) {
              console.log('[push] User IS subscribed.');
            } else {
              console.log('[push] User is NOT subscribed.');
            }

            updateBtn();
        });

    }

    /*
    * change the text if the user is subscribed or not
    */
    function updateBtn() {
        if (Notification.permission === 'denied') {
           // pushButtonModalText.textContent = Yii.t('js','Notifications are locked');
           // pushButtonModal.disabled = true;
           updateSubscriptionOnServer(null);
           return;
         }

        if (isSubscribed) {
            $('.js-push-btn-modal').addClass('checked')
           // pushButtonModalText.textContent = Yii.t('js','Disable');
           // $('.js-push-btn-modal').prop('data-target', 'pushDisableModal');
            $('.js-push-btn').hide();
            $('.js-push-btn-remove').show();
         } else {
             $('.js-push-btn-modal').removeClass('checked')
           // pushButtonModalText.textContent = Yii.t('js','Enable');
           //  $('.js-push-btn-modal').prop('data-target', 'pushEnableModal');
             $('.js-push-btn').show();
             $('.js-push-btn-remove').hide();
         }

         // pushButtonModal.disabled = false;
   }

    /*
     * SUBSCRIBE A USER
     */
    function subscribeUser() {
        const applicationServerKey = urlBase64ToUint8Array(applicationServerPublicKey);
        swRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: applicationServerKey
        })
        .then(function(subscription) {
            console.log('[push] User is now subscribed.', subscription);
            updateSubscriptionOnServer(subscription);
            isSubscribed = true;
            updateBtn();
        })
        .catch(function(err) {
            console.log('[push] Failed to subscribe the user: ', err);
            updateBtn();
        });
    }
    /*
    * UNSUBSCRIBE A USER
    */
    function unsubscribeUser() {
      swRegistration.pushManager.getSubscription()
      .then(function(subscription) {
        if (subscription) {
          return subscription.unsubscribe();
        }
      })
      .catch(function(error) {
        console.log('[push] Error unsubscribing', error);
      })
      .then(function() {
        updateSubscriptionOnServer(null);

        console.log('[push] User is now unsubscribed.');
        isSubscribed = false;

        updateBtn();
      });
    }

    /*
     *  Send subscription to application server
    */
    function updateSubscriptionOnServer(subscription) {
        console.log('[push] funzione updateSubscriptionOnServer ',subscription);
        if (subscription) {
            var sub = JSON.stringify(subscription);
            console.log('[push] Salvo la sottoscrizione',subscription);
        }else{
            var sub = JSON.stringify(null);
            console.log('[push] Elimino la sottoscrizione');
        }

        $.ajax({
            url: '{$urlSavesubscription}',
            type: "POST",
            data: {subscription: sub},
            dataType: "json",
            success:function(res){
                console.log(res.response);

            },
            error: function(j){
                console.log('[push] ERRORE Update subscription',j);
            }
        });
    }








});






JS;
$this->registerJs(
    $managePush,
    yii\web\View::POS_READY, //POS_END
    'managePush'
);

?>
