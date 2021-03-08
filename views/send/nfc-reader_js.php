<?php
use yii\web\View;

$wallet_nfc = <<<JS

if (("NDEFReader" in window)){
    var scanButton = document.querySelector('#activate-nfc-reader');

    scanButton.addEventListener("click", async () => {
      console.log("[nfc] User clicked scan button");

      try {
        const ndef = new NDEFReader();
        await ndef.scan();
        console.log("[nfc] > Scan started");

        ndef.addEventListener("readingerror", () => {
          console.log("[nfc] Argh! Cannot read data from the NFC tag. Try another one?");
        });

        ndef.addEventListener("reading", ({ message, serialNumber }) => {
          console.log(`[nfc]> Serial Number: `+ serialNumber);
          console.log(`[nfc]> Records: (`+message.records.length);
          $('#sendtokenform-to').val(message.address);
        });
      } catch (error) {
        console.log("[nfc] Argh! " + error);
      }
    });

    // const ndef = new NDEFReader();
    // ndef.scan().then(() => {
    //   console.log("[nfc] Scan started successfully.");
    //   ndef.onreadingerror = () => {
    //     console.log("[nfc] Cannot read data from the NFC tag. Try another one?");
    //   };
    //   ndef.onreading = event => {
    //       const message = event.message;
    //       for (const record of message.records) {
    //         console.log("[nfc] Record type:  " + record.recordType);
    //         console.log("[nfc] MIME type:    " + record.mediaType);
    //         console.log("[nfc] Record id:    " + record.id);
    //         switch (record.recordType) {
    //           case "text":
    //             // TODO: Read text record with record data, lang, and encoding.
    //             break;
    //           case "url":
    //             // TODO: Read URL record with record data.
    //             break;
    //           default:
    //             // TODO: Handle other records with record data.
    //         }
    //       }
    //     };
    // }).catch(error => {
    //   console.log(`[nfc] Error! Scan failed to start: .`);
    // });
}else{
    console.log("[nfc] NFC not enabled.");
}



JS;

$this->registerJs(
    $wallet_nfc,
    View::POS_READY, //POS_END
    'wallet_nfc'
);
