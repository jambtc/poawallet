/**
 * Created by jambtc
 */

var spinner = '<div class="button-spinner spinner-border text-primary" role="status">'
                 +'<span class="sr-only">Loading...</span></div>';

var notify;


$(function () {
    'use strict';

    notify = {
        check: function()
        {
            $.ajax({
                url: "index.php?r=backend/notify",
                type: "POST",
                //data: { 'countedNews' : $('#countedNews').val() },
                dataType: 'json',
                success: function(response) {
                    // console.log('[Backend] notify response:',response);
                    notify.handleResponse(response);
                    setTimeout(function(){ notify.check() }, 5000);
                },
                error: function(data) {
                    console.log('errore notifica. da mettere 60000');
                    setTimeout(function(){ notify.check() }, 5000);
                }
            });
        },
        handleResponse: function(response)
        {
            if (response.playAlarm == true){
                backend.Alarm();
            }
            if (response.playSound == true){
                backend.playSound();
                //VERIFICO QUESTE ULTIME 3 TRANSAZIONI PER AGGIORNARE IN REAL-TIME LO STATO (IN CASO CI SI TROVA SULLA PAGINA TRANSACTIONS)
                // for (var key in response.status) {
                //     var status = response.status[key];
                //     //backend.updateTransactionRows(status,key);
                // }
            }

            // $("#notifiche_dropdown").fadeIn(1000).css("display","");
            $('#quantity_notify').html(response.countedUnread);
            // $('#notifiche__contenuto').html(response.htmlTitle);
            // $('#notifiche__contenuto').append(response.htmlContent);

            $('.notification-list').html(response.htmlTitle);
            $('.notification-list').append(response.htmlContent);
            if (response.countedUnread > 0){
                $("#quantity_circle").fadeIn(1000).show();
                $('.notify-readAll').show();
            } else {
                 $("#quantity_circle").fadeIn(1000).hide();
                 $('.notify-readAll').hide();
            }
        },
        openAllEnvelopes: function(){
            $.ajax({
                url:'index.php?r=backend/update-all-news',
                type: "POST",
                data: {},
                dataType: 'json',
                success: function(response) {
                    console.log('[notify] All notifications updated.',response);
                },
                error: function(data) {
                    console.log(data);
                },
            });
        },

    };
    if($('#notification-list').length){
        // chiamo la funzione al refresh della pagina
        // se esiste l'header
        notify.check();
    }


});
