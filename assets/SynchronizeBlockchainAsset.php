<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\web\View;

/**
 * Blockchain Synchronization asset bundle.
 *
 * @author Sergio Casizzone <jambtc@gmail.com>
 * @since 2.0
 */
class SynchronizeBlockchainAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // 'css/site.css',
    ];
    public $js = [
        'js/synchBC-MainWorker.js'
    ];

    // public $jsOptions = [
    //     'getBlocknumberUrl' => 'index.php?r=blockchain/get-blocknumber',
    // ];


    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}
