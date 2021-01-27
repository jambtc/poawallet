$(document).ready(function () {
   $('#WalletTokenForm_amount').on('click', () => {
        show_easy_numpad();
    });
});



function show_easy_numpad() {
    var easy_numpad = `
        <div class="easy-numpad-frame-importo" id="easy-numpad-frame">
            <div class="easy-numpad-container">
                <div class="easy-numpad-output-container">
                    <p id="easy-numpad-output"></p>
                </div>
                <div class="easy-numpad-number-container">
                    <table border=0>
                        <tr>
                            <td width="33%"><span onclick="easynum(7)">7</span></td>
                            <td width="33%"><span onclick="easynum(8)">8</span></td>
                            <td width="33%"><span onclick="easynum(9)">9</span></td>
                        </tr>
                        <tr>
                            <td><span onclick="easynum(4)">4</span></td>
                            <td><span onclick="easynum(5)">5</span></td>
                            <td><span onclick="easynum(6)">6</span></td>

                        </tr>
                        <tr>
                            <td><span onclick="easynum(1)">1</a></td>
                            <td><span onclick="easynum(2)">2</a></td>
                            <td><span onclick="easynum(3)">3</a></td>
                        </tr>
                        <tr>
                            <td><span onclick="easynum(0)">0</span></td>
                            <td><span onclick="easynum(\'.\')">.</span></td>
                            <td><span class="del" id="del" onclick="easy_numpad_del()"><</span></td>
                        </tr>
                    </table>
                    <div style="text-align:right;  margin-right:15px; margin-top: -20px;">
                        <button class="btn btn-primary" onclick="easy_numpad_close()" id="easy_numpad_close_button">
                            Chiudi</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('.card-body-numpad').append(easy_numpad);
}

function easy_numpad_close() {
    $('.easy-numpad-frame-importo').remove();
}



function easynum(num) {
    event.stopPropagation();

    navigator.vibrate = navigator.vibrate || navigator.webkitVibrate || navigator.mozVibrate || navigator.msVibrate;
    if (navigator.vibrate) {
        navigator.vibrate(60);
    }

    var easy_num_text = $('#easy-numpad-output').text();

    if (isNaN(num)){
        if(!easy_num_text.includes('.'))
            //$('#easy-numpad-output').text(easy_num_text+num);
            $('#easy-numpad-output').append(num);
    }else{
        if (eval(easy_num_text) == 0){
            if (easy_num_text.includes('.'))
                $('#easy-numpad-output').append(num);
            else if (num != 0)
                $('#easy-numpad-output').text(num);
        }
        else
            $('#easy-numpad-output').append(num);
    }

    $('#WalletTokenForm_amount').val(  $('#easy-numpad-output').text()*1 );

}
function easy_numpad_del() {
    event.preventDefault();
    var easy_numpad_output_val = $('#easy-numpad-output').text();
    var easy_numpad_output_val_deleted = easy_numpad_output_val.slice(0, -1);
    if (easy_numpad_output_val_deleted == '')
        easy_numpad_output_val_deleted = 0;
    $('#easy-numpad-output').text(easy_numpad_output_val_deleted);
    $('#bitpay-pairing__message').text('');
}

function easy_numpad_clear() {
    event.preventDefault();
    $('#easy-numpad-output').text("");
}
