var pin_numpad = `
	<div class="easy-numpad-frame" id="easy-numpad-frame">
		<div class="easy-numpad-container">
			<div class="easy-numpad-output-container">
				<p class="easy-numpad-output alert alert-dark">
					<div class="row h2" style="margin-left:0px;margin-top:-56px;">
						<div class="col">
							<i class="fa fa-circle easy-dot-0"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-1"></i>
						</div>
							<div class="col">
							<i class="fa fa-circle easy-dot-2"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-3"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-4"></i>
						</div>
					</div>
				</p>
			</div>
			<div class="easy-numpad-number-container">
				<table border=0>
					<tr>
						<td width="33%"><span onclick="easypin(7)">7</span></td>
						<td width="33%"><span onclick="easypin(8)">8</span></td>
						<td width="33%"><span onclick="easypin(9)">9</span></td>
					</tr>
					<tr>
						<td><span onclick="easypin(4)">4</span></td>
						<td><span onclick="easypin(5)">5</span></td>
						<td><span onclick="easypin(6)">6</span></td>

					</tr>
					<tr>
						<td><span onclick="easypin(1)">1</a></td>
						<td><span onclick="easypin(2)">2</a></td>
						<td><span onclick="easypin(3)">3</a></td>
					</tr>
					<tr>
						<td><span onclick="easypin(0)">0</span></td>
						<td><span class="del" id="del" onclick="easy_pin_del(false)"><</span></td>
						<td><span class="clear" id="clear" onclick="easy_pin_clear()">C</span></td>
					</tr>
				</table>
				<p class="alert alert-danger invalid-feedback pin_password_em_" style="font-size:12px; text-align:right;" ></p>
			</div>
		</div>
	</div>
`;

var pin_confirm_numpad = `
	<div class="easy-numpad-frame" id="easy-numpad-frame">
		<div class="easy-numpad-container">
			<div class="easy-numpad-output-container">
				<p class="easy-numpad-output alert alert-dark">
					<div class="row h2" style="margin-left:0px;margin-top:-56px;">
						<div class="col">
							<i class="fa fa-circle easy-dot-0"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-1"></i>
						</div>
							<div class="col">
							<i class="fa fa-circle easy-dot-2"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-3"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-4"></i>
						</div>
					</div>
				</p>
			</div>
			<div class="easy-numpad-number-container">
				<table border=0>
					<tr>
						<td width="33%"><span onclick="easypinconfirm(7)">7</span></td>
						<td width="33%"><span onclick="easypinconfirm(8)">8</span></td>
						<td width="33%"><span onclick="easypinconfirm(9)">9</span></td>
					</tr>
					<tr>
						<td><span onclick="easypinconfirm(4)">4</span></td>
						<td><span onclick="easypinconfirm(5)">5</span></td>
						<td><span onclick="easypinconfirm(6)">6</span></td>

					</tr>
					<tr>
						<td><span onclick="easypinconfirm(1)">1</a></td>
						<td><span onclick="easypinconfirm(2)">2</a></td>
						<td><span onclick="easypinconfirm(3)">3</a></td>
					</tr>
					<tr>
						<td><span onclick="easypinconfirm(0)">0</span></td>
						<td><span class="del" id="del" onclick="easy_pin_del(true)"><</span></td>
						<td><span class="clear" id="clear" onclick="easy_pin_clear(false)">C</span></td>
					</tr>
				</table>
				<p class="alert alert-danger invalid-feedback pin_password_em_" style="font-size:12px; text-align:right;" ></p>
			</div>
		</div>
	</div>
`;
var pin_ask_numpad = `
	<div class="easy-numpad-frame" id="easy-numpad-frame">
		<div class="easy-numpad-container">
			<div class="easy-numpad-output-container">
				<p class="easy-numpad-output alert alert-dark">
					<div class="row h2" style="margin-left:0px;margin-top:-56px;">
						<div class="col">
							<i class="fa fa-circle easy-dot-0"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-1"></i>
						</div>
							<div class="col">
							<i class="fa fa-circle easy-dot-2"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-3"></i>
						</div>
						<div class="col">
							<i class="fa fa-circle easy-dot-4"></i>
						</div>
					</div>
				</p>
			</div>
			<div class="easy-numpad-number-container">
				<table border=0>
					<tr>
						<td width="33%"><span onclick="easypinconfirm(7)">7</span></td>
						<td width="33%"><span onclick="easypinconfirm(8)">8</span></td>
						<td width="33%"><span onclick="easypinconfirm(9)">9</span></td>
					</tr>
					<tr>
						<td><span onclick="easypinconfirm(4)">4</span></td>
						<td><span onclick="easypinconfirm(5)">5</span></td>
						<td><span onclick="easypinconfirm(6)">6</span></td>
					</tr>
					<tr>
						<td><span onclick="easypinconfirm(1)">1</a></td>
						<td><span onclick="easypinconfirm(2)">2</a></td>
						<td><span onclick="easypinconfirm(3)">3</a></td>
					</tr>
					<tr>
						<td><span onclick="easypinconfirm(0)">0</span></td>
						<td><span class="del" id="del" onclick="easy_pin_del(true)"><</span></td>
						<td><span class="clear" id="clear" onclick="easy_pin_clear(false)">C</span></td>
					</tr>
				</table>
				<p class="alert alert-danger invalid-feedback pin_password_em_" style="font-size:12px; text-align:right;" ></p>
			</div>
		</div>
	</div>
`;
navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;

function easypin(num) {
  event.stopPropagation();
	var pinDigits = 4; //in tutto richiede 5 cifre

  if (navigator.vibrate){
    navigator.vibrate(60);
  }
  var pin = $('#pin_password').val();
	console.log('lunghezza pin',pin.length);

	if (pin.length <= pinDigits){
		//$('.easy-numpad-output').append('*');

		$('.easy-dot-'+pin.length).removeClass('fa-circle').addClass('fa-check-circle');

	  $('#pin_password').val( pin + num );
	}
	if (pin.length == pinDigits){
		$('#pinNewButton').prop('disabled', false);
		$('#pinNewButton').removeClass('disabled');
		$('.easy-numpad-output').removeClass('alert-dark').addClass('alert-primary');
	}
}

// cancella un singolo carattere del pin
function easy_pin_del(confirm=false) {
  event.preventDefault();
	if (navigator.vibrate){
    navigator.vibrate(60);
  }

	$('#pinNewButton').prop('disabled', true);
	$('#pinNewButton').addClass('disabled');

  var easy_numpad_output_val = $('.easy-numpad-output').html();
	var easy_numpad_output_val_deleted = easy_numpad_output_val.slice(0, -1);
	console.log('[pin] easy_numpad_output_val:',easy_numpad_output_val);
	console.log('[pin] easy_numpad_output_val_deleted:',easy_numpad_output_val_deleted);

	$('.easy-numpad-output').text(easy_numpad_output_val_deleted);
	$('.invalid-feedback').hide().text('');

	if (confirm){
		var input = 'pin_password_confirm';
	}else{
		var input = 'pin_password';
	}
	var pin_password = $('#'+input).val();
  var pin_password_deleted = pin_password.slice(0, -1);

	console.log('[pin] pin_password:',pin_password);
	console.log('[pin] pin_password_deleted:',pin_password_deleted.length);

	$('.easy-dot-'+pin_password_deleted.length).removeClass('fa-check-circle').addClass('fa-circle');
	$('.easy-numpad-output').removeClass('alert-primary');
	$('.easy-numpad-output').removeClass('alert-danger');
	$('.easy-numpad-output').addClass('alert-dark');

	$('#'+input).val(pin_password_deleted);
}

//svuota il contenuto del pin
function easy_pin_clear(reset=true) {
  event.preventDefault();

	if (navigator.vibrate){
    navigator.vibrate(60);
  }
	//$('.easy-numpad-output').text('');
	for (i = 0; i < 5; i++)
		$('.easy-dot-'+i).removeClass('fa-check-circle').addClass('fa-circle');
	$('.easy-numpad-output').removeClass('alert-primary');
	$('.easy-numpad-output').removeClass('alert-danger');
	$('.easy-numpad-output').addClass('alert-dark');
	$('.invalid-feedback').hide().text('');

	$('.pin_password_em_').text('');
	$('#pin_password_confirm').val('');

	if (reset){
		$('#pin_password').val('');
	}
}

// elimina il numpad dalla pagina
function dropNumpad(reset=false){
	$('.easy-numpad-frame').remove();
	$('.easy-numpad-output').text('');
	if (reset){
		$('.pin_password_em_').text('');
		$('#pin_password').val('');
		$('#pin_password_confirm').val('');
	}
}

function easypinconfirm(num) {
  event.stopPropagation();
	var pinDigits = 4; //in tutto richiede 5 cifre

  if (navigator.vibrate) {
    navigator.vibrate(60);
  }
  var pin = $('#pin_password_confirm').val();
	console.log('lunghezza conferma pin',pin.length);

	if (pin.length <= pinDigits){
		//$('.easy-numpad-output').append('*');
		$('.easy-dot-'+pin.length).removeClass('fa-circle').addClass('fa-check-circle');
	  $('#pin_password_confirm').val( pin + num );
	}
	if (pin.length == pinDigits){
		console.log('verifica uguaglianza pin',$('#pin_password').val(),$('#pin_password_confirm').val());
		if ($('#pin_password').val() != $('#pin_password_confirm').val()){
			console.log('i pin sono diversi');
			$('#pinVerifyButton').prop('disabled', true);
			$('#pinVerifyButton').addClass('disabled');

			$('#pinRequestButton').prop('disabled', true);
			$('#pinRequestButton').addClass('disabled');

			$('#pinRemoveButton').prop('disabled', true);
			$('#pinRemoveButton').addClass('disabled');

			$('.pin_password_em_').show();
			$('.pin_password_em_').text('Pin is wrong!');
			//$('.easy-numpad-output').text('Pin is wrong!');
			$('.easy-numpad-output').removeClass('alert-dark').addClass('alert-danger');
			return false;
		}else{
			console.log('i pin sono uguali');
			$('#pinVerifyButton').prop('disabled', false);
			$('#pinVerifyButton').removeClass('disabled');

			$('#pinRequestButton').prop('disabled', false);
			$('#pinRequestButton').removeClass('disabled');

			$('#pinRemoveButton').prop('disabled', false);
			$('#pinRemoveButton').removeClass('disabled');

			$('.easy-numpad-output').removeClass('alert-dark').addClass('alert-primary');
		}

	}
}
/*
* aggiorno il timestamp del pin ad ogni refresh
*/
function updatePinTimestamp(){
	isPinRequest = false;
	dropNumpad(true);
	readAllData('pin')
		.then(function(old) {
			var post = {
				id		: new Date().getTime() /1000 | 0, // timestamp
				stop	: old[0].stop,
				pin     : old[0].pin,
			};

			clearAllData('pin')
				.then(function(){
					writeData('pin', post)
						.then(function() {
							console.log('[Pin Utility] Save updated pin info in indexedDB', post);
							$('#pinRequestModal').modal("hide");
							$('#pinRequestButton').text("Confirm");
						})
				})
		})
	return isPinRequest;
}

// funzione che richiede il pin dopo la scadenza del timestamp impostato in indexedDB
function askPin(crypted_pin,mask=0){
	$.ajax({
		url:'index.php?r=wallet/decrypt',
		type: "POST",
		data: {'pass': crypted_pin},
		dataType: "json",
		success:function(data){
			console.log('[Pin Utility] decrypted pin code',data);
			$('#pin_password').val(data.decrypted);
			$('.pin-confirm-numpad').append(pin_ask_numpad);
			$('.easy-numpad-frame').css("top","1px");

			if (mask==0){
				$('#pinRequestModal').modal({
					backdrop: 'static',
					keyboard: false
				});
			}else{
				$('#pinRemoveModal').modal({
		      backdrop: 'static',
		      keyboard: false
		    });
			}
		},
		error: function(j){
			console.log('error');
		}
	});
}
