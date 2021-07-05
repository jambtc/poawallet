<?php
use yii\helpers\Url;
use yii\web\View;

$options = [
    'language' => Yii::$app->language,
    'getTokens' => Url::to(['/settings/nodes/tokens-list']),
    'spinner' => '<div class="button-spinner spinner-border text-primary" style="width:1.3rem; height:1.3rem;" role="status"><span class="sr-only">Loading...</span></div>',
    // ...
];
$this->registerJs(
    "var yiiOptions = ".\yii\helpers\Json::htmlEncode($options).";",
    View::POS_HEAD,
    'yiiOptions'
);



$nodes = <<<JS
$(function(){

    $("#nodes-id_blockchain").change(function() {
        var idBlockchain = this.value;
        $.ajax({
            url: yiiOptions.getTokens,
            dataType: "json",
            data: {
                id: idBlockchain
            },
            beforeSend: function() {
                $("#nodes-id_smart_contract").hide().after(yiiOptions.spinner);
            },
            success: function(result) {
                //console.log(result);
                $("#nodes-id_smart_contract").show();
                $(".button-spinner").remove();
                var my_list = $("#nodes-id_smart_contract").empty();
                $.each(result, function(i, v){
                    my_list.append($("<option>").attr('value',i).text(v));
                });
            }
        });
    });



});
JS;

$this->registerJs(
    $nodes,
    View::POS_READY, //POS_END
    'nodes'
);
