/**
 * Created by jambtc
 */

var userWalletAddress;

if (("NDEFReader" in window)){
    $('.nfc-write-body').show();
    $.ajax({
        url : 'index.php?r=receive/get-address',
        type: "GET",
        dataType: "json",
        success:function(data)
        {
            userWalletAddress = data.wallet_address;
            console.log("[nfc] user addres is: ",userWalletAddress);

        }
    });
}

var writeButton = document.querySelector('#nfc-write-btn');

writeButton.addEventListener("click", async () => {
  console.log("[nfc] User clicked write button");
  $('#nfc-write-btn').removeClass('btn-secondary');
  $('#nfc-write-btn').addClass('btn-success');
  $('#nfc-write-text').text('NFC Ready');

  try {
    const ndef = new NDEFReader();
    await ndef.write(userWalletAddress);
  } catch (error) {
      console.log("[nfc] Error" + error);
  }
});
