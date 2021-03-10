/**
 * Created by jambtc
 */

 //al click sull'indirizzo token in RICEVI lo copia negli appunti
 $(".copyonClickAddress").click(function(){
     if (!navigator.clipboard) {
         fallbackCopyTextToClipboard();
         return;
     }
     navigator.clipboard.writeText($('#inputcopyWalletAddress').val()).then(function() {
         //console.log('Async: Copying to clipboard was successful!');
         $('#copyAddressModal').modal('show');
     }, function(err) {
         console.log('Async: Could not copy text: ', err);
     });
 });

 //nel caso in cui non funzioni il navigator.clipboard, utilizzo java standard
 function fallbackCopyTextToClipboard() {
     var textArea = document.createElement("textarea");
     textArea.value = $('#inputcopyWalletAddress').val();
     document.body.appendChild(textArea);
     textArea.focus();
     textArea.select();

     try {
         var successful = document.execCommand('copy');
         var msg = successful ? 'successful' : 'unsuccessful';
         console.log('Fallback: Copying text command was ' + msg);
         $('#copyAddressModal').modal('show');
     } catch (err) {
         console.log('Fallback: Oops, unable to copy', err);
     }

     document.body.removeChild(textArea);
 }
