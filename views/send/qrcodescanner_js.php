<script type="module">
  import QrScanner from "./js/qrcodescanner/qr-scanner/qr-scanner.min.js";
  QrScanner.WORKER_PATH = './js/qrcodescanner/qr-scanner/qr-scanner-worker.min.js';

  const video = document.getElementById('qr-video');
  const camQrResult = document.getElementById('sendform-to');

  function setResult(label, result) {
    console.log('[QRCode result]',result);

    var res = extractImport(result);
    console.log('[QRCode res]',res);

    $('#sendform-to').val(res.address);
    $('#sendform-amount').val(res.amount);
    if (res.amount != ''){
        $('#sendform-amount').prop('readonly','readonly');
    }
    $('#sendform-memo').val(res.message);
    if (res.message != ''){
        $('#sendform-memo').prop('readonly','readonly');
    }

    $('#cameraPopup').hide();
    scanner.stop();
  }

  // estrae un eventuale importo dal qrcode
  function extractImport(result){
    var str = result;
    var spl = str.split("&",3);

    console.log('[QRCode split]',spl);

    if (typeof spl[1] !== "undefined"){
      var amount = spl[1].split("=",2);
      spl[1] = amount[1];
    }else{
      spl[1] = '';
    }
    if (typeof spl[2] !== "undefined"){
      var memo = spl[2].split("=",2);
      spl[2] = memo[1];
    }else{
      spl[2] = '';
    }
    var ret = {'address':spl[0],'amount':spl[1],'message':spl[2]};
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
