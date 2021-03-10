<script type="module">
  import QrScanner from "./js/qrcodescanner/qr-scanner/qr-scanner.min.js";
  QrScanner.WORKER_PATH = './js/qrcodescanner/qr-scanner/qr-scanner-worker.min.js';

  const video = document.getElementById('qr-video');
  const camQrResult = document.getElementById('sendform-to');

  function setResult(label, result) {
    console.log('[QRCode result]',result);

    var res = extractImport(result);

    $('#sendform-to').val(res.address);
    $('#sendform-amount').val(res.amount);
    $('#cameraPopup').hide();
    scanner.stop();
  }

  // estrae un eventuale importo dal qrcode
  function extractImport(result){
    var str = result;
    var spl = str.split("?",2);

    if (typeof spl[1] !== "undefined"){
    // if (spl[1].length != 'undefined'){
      var amount = spl[1].split("=",2);
      spl[1] = amount[1];
    }else{
      spl[1] = '';
    }
    var ret = {'address':spl[0],'amount':spl[1]};
    return ret;
  }
	const scanner = new QrScanner(video, result => setResult(camQrResult, result));

	// al click del pulsante photo attivo la fotocamera
    document.querySelector('#activate-camera-btn').addEventListener('click', function(){
        console.log('[qrcode] Open camera modal');
        $('#cameraPopup').show();
        //$('#popupCamera').appendTo("body").modal('show');
		scanner.start();
	});

	// all'uscita disattiva la cam
	document.querySelector('#camera-close').addEventListener('click', function(){
        $('#cameraPopup').hide();
		scanner.stop();
  });
</script>
