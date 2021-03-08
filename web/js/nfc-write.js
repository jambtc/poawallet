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
        }
    });
}

var writeButton = document.querySelector('#nfc-write-btn');

writeButton.addEventListener("click", async () => {
  console.log("User clicked write button");

  try {
    const ndef = new NDEFReader();
    await ndef.write(userWalletAddress);
  } catch (error) {
    log("Argh! " + error);
  }
});
